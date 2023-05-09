package com.job4u.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.*;
import com.codename1.ui.events.ActionListener;
import com.job4u.entities.EventCategory;
import com.job4u.utils.Statics;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

public class EventCategoryService {

    public static EventCategoryService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<EventCategory> listEventCategorys;


    private EventCategoryService() {
        cr = new ConnectionRequest();
    }

    public static EventCategoryService getInstance() {
        if (instance == null) {
            instance = new EventCategoryService();
        }
        return instance;
    }

    public ArrayList<EventCategory> getAll() {
        listEventCategorys = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/eventCategory");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listEventCategorys = getList();
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

        return listEventCategorys;
    }

    private ArrayList<EventCategory> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                EventCategory eventCategory = new EventCategory(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        (String) obj.get("description"),
                        (String) obj.get("name")

                );

                listEventCategorys.add(eventCategory);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return listEventCategorys;
    }

    public int add(EventCategory eventCategory) {

        cr = new ConnectionRequest();

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/eventCategory/add");

        cr.addArgument("description", eventCategory.getDescription());
        cr.addArgument("name", eventCategory.getName());


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

    public int edit(EventCategory eventCategory) {

        cr = new ConnectionRequest();
        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/eventCategory/edit");
        cr.addArgument("id", String.valueOf(eventCategory.getId()));

        cr.addArgument("description", eventCategory.getDescription());
        cr.addArgument("name", eventCategory.getName());


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

    public int delete(int eventCategoryId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/eventCategory/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(eventCategoryId));

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
