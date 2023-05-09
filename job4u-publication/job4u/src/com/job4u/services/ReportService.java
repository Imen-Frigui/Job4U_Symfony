package com.job4u.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.*;
import com.codename1.ui.events.ActionListener;
import com.job4u.entities.Poste;
import com.job4u.entities.Report;
import com.job4u.entities.User;
import com.job4u.utils.Statics;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

public class ReportService {

    public static ReportService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<Report> listReports;


    private ReportService() {
        cr = new ConnectionRequest();
    }

    public static ReportService getInstance() {
        if (instance == null) {
            instance = new ReportService();
        }
        return instance;
    }

    public ArrayList<Report> getAll() {
        listReports = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/report");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listReports = getList();
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

        return listReports;
    }

    private ArrayList<Report> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                Report report = new Report(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        makeUser((Map<String, Object>) obj.get("user")),
                        makePoste((Map<String, Object>) obj.get("poste")),
                        (String) obj.get("type"),
                        (String) obj.get("description")

                );

                listReports.add(report);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return listReports;
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

    public Poste makePoste(Map<String, Object> obj) {
        if (obj == null) {
            return null;
        }
        Poste poste = new Poste();
        poste.setId((int) Float.parseFloat(obj.get("id").toString()));
        poste.setTitre((String) obj.get("titre"));
        return poste;
    }

    public int add(Report report) {

        cr = new ConnectionRequest();

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/report/add");

        cr.addArgument("user", String.valueOf(report.getUser().getId()));
        cr.addArgument("poste", String.valueOf(report.getPoste().getId()));
        cr.addArgument("type", report.getType());
        cr.addArgument("description", report.getDescription());


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

    public int edit(Report report) {

        cr = new ConnectionRequest();
        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/report/edit");
        cr.addArgument("id", String.valueOf(report.getId()));

        cr.addArgument("user", String.valueOf(report.getUser().getId()));
        cr.addArgument("poste", String.valueOf(report.getPoste().getId()));
        cr.addArgument("type", report.getType());
        cr.addArgument("description", report.getDescription());


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

    public int delete(int reportId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/report/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(reportId));

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
