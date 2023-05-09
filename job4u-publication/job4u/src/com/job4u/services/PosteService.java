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

public class PosteService {

    public static PosteService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<Poste> listPostes;



    private PosteService() {
        cr = new ConnectionRequest();
    }

    public static PosteService getInstance() {
        if (instance == null) {
            instance = new PosteService();
        }
        return instance;
    }

    public ArrayList<Poste> getAll() {
        listPostes = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/poste");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listPostes = getList();
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

        return listPostes;
    }

    private ArrayList<Poste> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                Poste poste = new Poste(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        makeUser((Map<String, Object>) obj.get("user")),
                        (String) obj.get("titre"),
                        (String) obj.get("description"),
                        (String) obj.get("image"),
                        (String) obj.get("domaine"),
                        new SimpleDateFormat("dd-MM-yyyy").parse((String) obj.get("date"))

                );

                listPostes.add(poste);
            }
        } catch (IOException | ParseException e) {
            e.printStackTrace();
        }
        return listPostes;
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

    public int add(Poste poste) {

        MultipartRequest cr = new MultipartRequest();
        cr.setFilename("file", "Poste.jpg");


        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/poste/add");

        cr.addArgumentNoEncoding("user", String.valueOf(poste.getUser().getId()));
        cr.addArgumentNoEncoding("titre", poste.getTitre());
        cr.addArgumentNoEncoding("description", poste.getDescription());
        cr.addArgumentNoEncoding("image", poste.getImage());
        cr.addArgumentNoEncoding("domaine", poste.getDomaine());
        cr.addArgumentNoEncoding("date", new SimpleDateFormat("dd-MM-yyyy").format(poste.getDate()));



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

    public int edit(Poste poste, boolean imageEdited) {

        MultipartRequest cr = new MultipartRequest();
        cr.setFilename("file", "Poste.jpg");

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/poste/edit");
        cr.addArgumentNoEncoding("id", String.valueOf(poste.getId()));

        if (imageEdited) {
            try {
                cr.addData("file", poste.getImage(), "image/jpeg");
            } catch (IOException e) {
                e.printStackTrace();
            }
        } else {
            cr.addArgumentNoEncoding("image", poste.getImage());
        }

        cr.addArgumentNoEncoding("user", String.valueOf(poste.getUser().getId()));
        cr.addArgumentNoEncoding("titre", poste.getTitre());
        cr.addArgumentNoEncoding("description", poste.getDescription());
        cr.addArgumentNoEncoding("image", poste.getImage());
        cr.addArgumentNoEncoding("domaine", poste.getDomaine());
        cr.addArgumentNoEncoding("date", new SimpleDateFormat("dd-MM-yyyy").format(poste.getDate()));



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

    public int delete(int posteId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/poste/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(posteId));

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
