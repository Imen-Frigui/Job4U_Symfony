package com.job4u.gui.back.notification;


import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Event;
import com.job4u.entities.Notification;
import com.job4u.entities.User;
import com.job4u.services.EventService;
import com.job4u.services.NotificationService;
import com.job4u.services.UserService;

import java.util.ArrayList;

public class ModifierNotification extends Form {


    Notification currentNotification;

    TextField messageTF;
    TextField statusTF;
    Label messageLabel;
    Label statusLabel;
    PickerComponent createdAtTF;
    CheckBox hasReadCB;
    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    ArrayList<Event> listEvents;
    PickerComponent eventPC;
    Event selectedEvent = null;


    Button manageButton;

    Form previous;

    public ModifierNotification(Form previous) {
        super("Modifier", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        currentNotification = AfficherToutNotification.currentNotification;

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


        messageLabel = new Label("Message : ");
        messageLabel.setUIID("labelDefault");
        messageTF = new TextField();
        messageTF.setHint("Tapez le message");


        hasReadCB = new CheckBox("HasRead : ");


        createdAtTF = PickerComponent.createDate(null).label("CreatedAt");


        statusLabel = new Label("Status : ");
        statusLabel.setUIID("labelDefault");
        statusTF = new TextField();
        statusTF.setHint("Tapez le status");


        messageTF.setText(currentNotification.getMessage());
        if (currentNotification.getHasRead() == 1) {
            hasReadCB.setSelected(true);
        }
        createdAtTF.getPicker().setDate(currentNotification.getCreatedAt());
        statusTF.setText(currentNotification.getStatus());

        userPC.getPicker().setSelectedString(currentNotification.getUser().getEmail());
        selectedUser = currentNotification.getUser();
        eventPC.getPicker().setSelectedString(currentNotification.getEvent().getTitle());
        selectedEvent = currentNotification.getEvent();


        manageButton = new Button("Modifier");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(


                messageLabel, messageTF,
                hasReadCB,
                createdAtTF,
                statusLabel, statusTF,
                userPC, eventPC,
                manageButton
        );

        this.addAll(container);
    }

    private void addActions() {

        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = NotificationService.getInstance().edit(
                        new Notification(
                                currentNotification.getId(),


                                selectedUser,
                                selectedEvent,
                                messageTF.getText(),
                                hasReadCB.isSelected() ? 1 : 0,
                                createdAtTF.getPicker().getDate(),
                                statusTF.getText()

                        )
                );
                if (responseCode == 200) {
                    Dialog.show("Succés", "Notification modifié avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur de modification de notification. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh() {
        ((AfficherToutNotification) previous).refresh();
        previous.showBack();
    }

    private boolean controleDeSaisie() {


        if (messageTF.getText().equals("")) {
            Dialog.show("Avertissement", "Message vide", new Command("Ok"));
            return false;
        }


        if (createdAtTF.getPicker().getDate() == null) {
            Dialog.show("Avertissement", "Veuillez saisir la createdAt", new Command("Ok"));
            return false;
        }


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