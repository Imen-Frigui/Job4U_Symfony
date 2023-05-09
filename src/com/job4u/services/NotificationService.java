package com.job4u.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.*;
import com.codename1.ui.events.ActionListener;
import com.job4u.entities.Event;
import com.job4u.entities.Notification;
import com.job4u.entities.User;
import com.job4u.utils.Statics;

import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

public class NotificationService {

    public static NotificationService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<Notification> listNotifications;


    private NotificationService() {
        cr = new ConnectionRequest();
    }

    public static NotificationService getInstance() {
        if (instance == null) {
            instance = new NotificationService();
        }
        return instance;
    }

    public ArrayList<Notification> getAll() {
        listNotifications = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/notification");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listNotifications = getList();
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

        return listNotifications;
    }

    private ArrayList<Notification> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");



            for (Map<String, Object> obj : list) {
                int hasRead = 0;
                if (obj.get("hasRead").toString().equals("true")) hasRead = 1;

                Notification notification = new Notification(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        makeUser((Map<String, Object>) obj.get("user")),
                        makeEvent((Map<String, Object>) obj.get("event")),
                        (String) obj.get("message"),
                        hasRead,
                        new SimpleDateFormat("dd-MM-yyyy").parse((String) obj.get("createdAt")),
                        (String) obj.get("status")

                );

                listNotifications.add(notification);
            }
        } catch (IOException | ParseException e) {
            e.printStackTrace();
        }
        return listNotifications;
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

    public Event makeEvent(Map<String, Object> obj) {
        if (obj == null) {
            return null;
        }
        Event event = new Event();
        event.setId((int) Float.parseFloat(obj.get("id").toString()));
        event.setTitle((String) obj.get("title"));
        return event;
    }

    public int add(Notification notification) {

        cr = new ConnectionRequest();

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/notification/add");

        cr.addArgument("user", String.valueOf(notification.getUser().getId()));
        cr.addArgument("event", String.valueOf(notification.getEvent().getId()));
        cr.addArgument("message", notification.getMessage());
        cr.addArgument("hasRead", String.valueOf(notification.getHasRead()));
        cr.addArgument("createdAt", new SimpleDateFormat("dd-MM-yyyy").format(notification.getCreatedAt()));
        cr.addArgument("status", notification.getStatus());


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

    public int edit(Notification notification) {

        cr = new ConnectionRequest();
        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/notification/edit");
        cr.addArgument("id", String.valueOf(notification.getId()));

        cr.addArgument("user", String.valueOf(notification.getUser().getId()));
        cr.addArgument("event", String.valueOf(notification.getEvent().getId()));
        cr.addArgument("message", notification.getMessage());
        cr.addArgument("hasRead", String.valueOf(notification.getHasRead()));
        cr.addArgument("createdAt", new SimpleDateFormat("dd-MM-yyyy").format(notification.getCreatedAt()));
        cr.addArgument("status", notification.getStatus());


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

    public int delete(int notificationId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/notification/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(notificationId));

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
