package com.job4u.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.*;
import com.codename1.ui.events.ActionListener;
import com.job4u.entities.Event;
import com.job4u.entities.EventCategory;
import com.job4u.entities.User;
import com.job4u.utils.Statics;

import java.io.IOException;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

public class EventService {

    public static EventService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<Event> listEvents;


    private EventService() {
        cr = new ConnectionRequest();
    }

    public static EventService getInstance() {
        if (instance == null) {
            instance = new EventService();
        }
        return instance;
    }

    public ArrayList<Event> getAll() {
        listEvents = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/event");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listEvents = getList();
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

        return listEvents;
    }

    private ArrayList<Event> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                Event event = new Event(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        makeUser((Map<String, Object>) obj.get("user")),
                        makeEventCategory((Map<String, Object>) obj.get("eventCategory")),
                        (String) obj.get("title"),
                        (String) obj.get("description"),
                        new SimpleDateFormat("dd-MM-yyyy").parse((String) obj.get("date")),
                        (String) obj.get("location"),
                        (String) obj.get("image")

                );

                listEvents.add(event);
            }
        } catch (IOException | ParseException e) {
            e.printStackTrace();
        }
        return listEvents;
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

    public EventCategory makeEventCategory(Map<String, Object> obj) {
        if (obj == null) {
            return null;
        }
        EventCategory eventCategory = new EventCategory();
        eventCategory.setId((int) Float.parseFloat(obj.get("id").toString()));
        eventCategory.setName((String) obj.get("name"));
        return eventCategory;
    }

    public int add(Event event) {

        MultipartRequest cr = new MultipartRequest();
        cr.setFilename("file", "Event.jpg");


        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/event/add");

        cr.addArgumentNoEncoding("image", event.getImage());

        cr.addArgumentNoEncoding("user", String.valueOf(event.getUser().getId()));
        cr.addArgumentNoEncoding("eventCategory", String.valueOf(event.getEventCategory().getId()));
        cr.addArgumentNoEncoding("title", event.getTitle());
        cr.addArgumentNoEncoding("description", event.getDescription());
        cr.addArgumentNoEncoding("date", new SimpleDateFormat("dd-MM-yyyy").format(event.getDate()));
        cr.addArgumentNoEncoding("location", event.getLocation());
        cr.addArgumentNoEncoding("image", event.getImage());


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

    public int edit(Event event, boolean imageEdited) {

        MultipartRequest cr = new MultipartRequest();
        cr.setFilename("file", "Event.jpg");

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/event/edit");
        cr.addArgumentNoEncoding("id", String.valueOf(event.getId()));

        if (imageEdited) {
            try {
                cr.addData("file", event.getImage(), "image/jpeg");
            } catch (IOException e) {
                e.printStackTrace();
            }
        } else {
            cr.addArgumentNoEncoding("image", event.getImage());
        }

        cr.addArgumentNoEncoding("user", String.valueOf(event.getUser().getId()));
        cr.addArgumentNoEncoding("eventCategory", String.valueOf(event.getEventCategory().getId()));
        cr.addArgumentNoEncoding("title", event.getTitle());
        cr.addArgumentNoEncoding("description", event.getDescription());
        cr.addArgumentNoEncoding("date", new SimpleDateFormat("dd-MM-yyyy").format(event.getDate()));
        cr.addArgumentNoEncoding("location", event.getLocation());
        cr.addArgumentNoEncoding("image", event.getImage());


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

    public int delete(int eventId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/event/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(eventId));

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
