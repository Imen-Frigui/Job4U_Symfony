package com.job4u.services;

import com.codename1.components.InfiniteProgress;
import com.codename1.io.*;
import com.codename1.ui.events.ActionListener;
import com.job4u.entities.Event;
import com.job4u.entities.Participant;
import com.job4u.entities.User;
import com.job4u.utils.Statics;

import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

public class ParticipantService {

    public static ParticipantService instance = null;
    public int resultCode;
    private ConnectionRequest cr;
    private ArrayList<Participant> listParticipants;


    private ParticipantService() {
        cr = new ConnectionRequest();
    }

    public static ParticipantService getInstance() {
        if (instance == null) {
            instance = new ParticipantService();
        }
        return instance;
    }

    public ArrayList<Participant> getAll() {
        listParticipants = new ArrayList<>();

        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/participant");
        cr.setHttpMethod("GET");

        cr.addResponseListener(new ActionListener<NetworkEvent>() {
            @Override
            public void actionPerformed(NetworkEvent evt) {

                if (cr.getResponseCode() == 200) {
                    listParticipants = getList();
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

        return listParticipants;
    }

    private ArrayList<Participant> getList() {
        try {
            Map<String, Object> parsedJson = new JSONParser().parseJSON(new CharArrayReader(
                    new String(cr.getResponseData()).toCharArray()
            ));
            List<Map<String, Object>> list = (List<Map<String, Object>>) parsedJson.get("root");

            for (Map<String, Object> obj : list) {
                Participant participant = new Participant(
                        (int) Float.parseFloat(obj.get("id").toString()),

                        makeUser((Map<String, Object>) obj.get("user")),
                        makeEvent((Map<String, Object>) obj.get("event")),
                        (String) obj.get("status")

                );

                listParticipants.add(participant);
            }
        } catch (IOException e) {
            e.printStackTrace();
        }
        return listParticipants;
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

    public int add(Participant participant) {

        cr = new ConnectionRequest();

        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/participant/add");

        cr.addArgument("user", String.valueOf(participant.getUser().getId()));
        cr.addArgument("event", String.valueOf(participant.getEvent().getId()));
        cr.addArgument("status", participant.getStatus());


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

    public int edit(Participant participant) {

        cr = new ConnectionRequest();
        cr.setHttpMethod("POST");
        cr.setUrl(Statics.BASE_URL + "/participant/edit");
        cr.addArgument("id", String.valueOf(participant.getId()));

        cr.addArgument("user", String.valueOf(participant.getUser().getId()));
        cr.addArgument("event", String.valueOf(participant.getEvent().getId()));
        cr.addArgument("status", participant.getStatus());


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

    public int delete(int participantId) {
        cr = new ConnectionRequest();
        cr.setUrl(Statics.BASE_URL + "/participant/delete");
        cr.setHttpMethod("POST");
        cr.addArgument("id", String.valueOf(participantId));

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
