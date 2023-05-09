package com.job4u.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.*;
import com.codename1.ui.events.ActionListener;
import com.job4u.entities.*;
import com.job4u.utils.*;

import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

public class CommentaireService {

    public static CommentaireService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<Commentaire> listCommentaires;



    private CommentaireService() {
        cr = new ConnectionRequest();
    }

    public static CommentaireService getInstance() {
        if (instance == null) {
            instance = new CommentaireService();
        }
        return instance;
    }

    public ArrayList<Commentaire> getAll() {
        listCommentaires = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/commentaire");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listCommentaires = getList();
                }

                cr.removeResponseListener(this);
            }
        });

        try {
            cr.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(cr);
        } catch (Exception e) {
            e.printStackTrace();
        }

        return listCommentaires;
    }

    private ArrayList<Commentaire> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                Commentaire commentaire = new Commentaire(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        makePoste((Map<String, Object>) obj.get("poste")),
                        makeUser((Map<String, Object>) obj.get("user")),
                        (String) obj.get("description"),
                        new SimpleDateFormat("dd-MM-yyyy").parse((String) obj.get("date"))
                );

                listCommentaires.add(commentaire);
            }
        } catch (IOException | ParseException e) {
            e.printStackTrace();
        }
        return listCommentaires;
    }

    public Poste makePoste(Map<String, Object> obj) {
        if (obj == null) {
            return null;
        }
        Poste poste = new Poste();
        poste.setId((int) Float.parseFloat(obj.get("id").toString()));
        poste.setTitre((String) obj.get("titre"));
        return poste;
    }

    public User makeUser(Map<String, Object> obj) {
        if (obj == null) {
            return null;
        }
        User user = new User();
        user.setId((int) Float.parseFloat(obj.get("id").toString()));
        user.setEmail((String) obj.get("email"));
        return user;
    }

    public int add(Commentaire commentaire) {

        cr = new ConnectionRequest();

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/commentaire/add");

        cr.addArgument("poste", String.valueOf(commentaire.getPoste().getId()));
        cr.addArgument("user", String.valueOf(commentaire.getUser().getId()));
        cr.addArgument("description", commentaire.getDescription());
        cr.addArgument("date", new SimpleDateFormat("dd-MM-yyyy").format(commentaire.getDate()));


        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                resultCode = cr.getResponseCode();
                cr.removeResponseListener(this);
            }
        });
        try {
            cr.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(cr);
        } catch (Exception ignored) {

        }
        return resultCode;
    }

    public int edit(Commentaire commentaire) {

        cr = new ConnectionRequest();
        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/commentaire/edit");
        cr.addArgument("id", String.valueOf(commentaire.getId()));

        cr.addArgument("poste", String.valueOf(commentaire.getPoste().getId()));
        cr.addArgument("user", String.valueOf(commentaire.getUser().getId()));
        cr.addArgument("description", commentaire.getDescription());
        cr.addArgument("date", new SimpleDateFormat("dd-MM-yyyy").format(commentaire.getDate()));


        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                resultCode = cr.getResponseCode();
                cr.removeResponseListener(this);
            }
        });
        try {
            cr.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(cr);
        } catch (Exception ignored) {

        }
        return resultCode;
    }

    public int delete(int commentaireId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/commentaire/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(commentaireId));

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {
                cr.removeResponseListener(this);
            }
        });

        try {
            cr.setDisposeOnCompletion(new InfiniteProgress().showInfiniteBlocking());
            NetworkManager.getInstance().addToQueueAndWait(cr);
        } catch (Exception e) {
            e.printStackTrace();
        }
        return cr.getResponseCode();
    }
}
