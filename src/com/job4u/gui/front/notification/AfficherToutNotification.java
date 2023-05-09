package com.job4u.gui.front.notification;


import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.util.Resources;
import com.job4u.entities.Notification;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.NotificationService;

import java.text.SimpleDateFormat;
import java.util.ArrayList;

public class AfficherToutNotification extends BaseForm {

    Form previous;

    public static Notification currentNotification = null;
    Button addBtn;


    public AfficherToutNotification(Resources res) {
        super(new BoxLayout(BoxLayout.Y_AXIS));

        Toolbar tb = new Toolbar(true);
        tb.setUIID("CustomToolbar");
        setToolbar(tb);
        getTitleArea().setUIID("Container");
        setTitle("Notification");
        getContentPane().setScrollVisible(false);

        super.addSideMenu(res);

        Image img = res.getImage("profile-background.jpg");
        if (img.getHeight() > Display.getInstance().getDisplayHeight() / 3) {
            img = img.scaledHeight(Display.getInstance().getDisplayHeight() / 3);
        }

        addGUIs();
        addActions();


    }

    public void refresh() {
        this.removeAll();
        addGUIs();
        addActions();
        this.refreshTheme();
    }

    private void addGUIs() {

        Container container = new Container();
        container.setPreferredH(250);
        this.add(container);

        addBtn = new Button("Ajouter");
        addBtn.setUIID("buttonWhiteCenter");
        this.add(addBtn);


        ArrayList<Notification> listNotifications = NotificationService.getInstance().getAll();


        if (listNotifications.size() > 0) {
            for (Notification notification : listNotifications) {
                Component model = makeNotificationModel(notification);
                this.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }

    private void addActions() {
        addBtn.addActionListener(action -> {
            currentNotification = null;
            new AjouterNotification(this).show();
        });

    }

    Label userLabel, eventLabel, messageLabel, hasReadLabel, createdAtLabel, statusLabel;


    private Container makeModelWithoutButtons(Notification notification) {
        Container notificationModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        notificationModel.setUIID("containerRounded");


        userLabel = new Label("User : " + notification.getUser());
        userLabel.setUIID("labelDefault");

        eventLabel = new Label("Event : " + notification.getEvent());
        eventLabel.setUIID("labelDefault");

        messageLabel = new Label("Message : " + notification.getMessage());
        messageLabel.setUIID("labelDefault");

        hasReadLabel = new Label("HasRead : " + (notification.getHasRead() == 1 ? "True" : "False"));
        hasReadLabel.setUIID("labelDefault");

        createdAtLabel = new Label("CreatedAt : " + new SimpleDateFormat("dd-MM-yyyy").format(notification.getCreatedAt()));
        createdAtLabel.setUIID("labelDefault");

        statusLabel = new Label("Status : " + notification.getStatus());
        statusLabel.setUIID("labelDefault");

        userLabel = new Label("User : " + notification.getUser().getEmail());
        userLabel.setUIID("labelDefault");

        eventLabel = new Label("Event : " + notification.getEvent().getTitle());
        eventLabel.setUIID("labelDefault");


        notificationModel.addAll(

                userLabel, eventLabel, messageLabel, hasReadLabel, createdAtLabel, statusLabel
        );

        return notificationModel;
    }

    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makeNotificationModel(Notification notification) {

        Container notificationModel = makeModelWithoutButtons(notification);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");

        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentNotification = notification;
            new ModifierNotification(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce notification ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = NotificationService.getInstance().delete(notification.getId());

                if (responseCode == 200) {
                    currentNotification = null;
                    dlg.dispose();
                    notificationModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du notification. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);


        notificationModel.add(btnsContainer);

        return notificationModel;
    }

}