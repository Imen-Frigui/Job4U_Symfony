package com.job4u.gui.back.participant;


import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Event;
import com.job4u.entities.Participant;
import com.job4u.entities.User;
import com.job4u.services.EventService;
import com.job4u.services.ParticipantService;
import com.job4u.services.UserService;

import java.util.ArrayList;

public class AjouterParticipant extends Form {


    TextField statusTF;
    Label statusLabel;


    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    ArrayList<Event> listEvents;
    PickerComponent eventPC;
    Event selectedEvent = null;


    Button manageButton;

    Form previous;

    public AjouterParticipant(Form previous) {
        super("Ajouter", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        addGUIs();
        addActions();


        getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e -> previous.showBack());
    }

    private void addGUIs() {

        String[] userStrings;
        int userIndex;
        userPC = PickerComponent.createStrings("").label("User");
        listUsers = UserService.getInstance().getAll();
        userStrings = new String[listUsers.size()];
        userIndex = 0;
        for (User user : listUsers) {
            userStrings[userIndex] = user.getEmail();
            userIndex++;
        }
        if (listUsers.size() > 0) {
            userPC.getPicker().setStrings(userStrings);
            userPC.getPicker().addActionListener(l -> selectedUser = listUsers.get(userPC.getPicker().getSelectedStringIndex()));
        } else {
            userPC.getPicker().setStrings("");
        }

        String[] eventStrings;
        int eventIndex;
        eventPC = PickerComponent.createStrings("").label("Event");
        listEvents = EventService.getInstance().getAll();
        eventStrings = new String[listEvents.size()];
        eventIndex = 0;
        for (Event event : listEvents) {
            eventStrings[eventIndex] = event.getTitle();
            eventIndex++;
        }
        if (listEvents.size() > 0) {
            eventPC.getPicker().setStrings(eventStrings);
            eventPC.getPicker().addActionListener(l -> selectedEvent = listEvents.get(eventPC.getPicker().getSelectedStringIndex()));
        } else {
            eventPC.getPicker().setStrings("");
        }


        statusLabel = new Label("Status : ");
        statusLabel.setUIID("labelDefault");
        statusTF = new TextField();
        statusTF.setHint("Tapez le status");


        manageButton = new Button("Ajouter");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(


                statusLabel, statusTF,
                userPC, eventPC,
                manageButton
        );

        this.addAll(container);
    }

    private void addActions() {

        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = ParticipantService.getInstance().add(
                        new Participant(


                                selectedUser,
                                selectedEvent,
                                statusTF.getText()
                        )
                );
                if (responseCode == 200) {
                    Dialog.show("Succés", "Participant ajouté avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur d'ajout de participant. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh() {
        ((AfficherToutParticipant) previous).refresh();
        previous.showBack();
    }

    private boolean controleDeSaisie() {


        if (statusTF.getText().equals("")) {
            Dialog.show("Avertissement", "Status vide", new Command("Ok"));
            return false;
        }


        if (selectedUser == null) {
            Dialog.show("Avertissement", "Veuillez choisir un user", new Command("Ok"));
            return false;
        }

        if (selectedEvent == null) {
            Dialog.show("Avertissement", "Veuillez choisir un event", new Command("Ok"));
            return false;
        }


        return true;
    }
}