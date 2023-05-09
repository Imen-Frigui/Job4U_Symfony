package com.job4u.gui.front.participant;


import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.util.Resources;
import com.job4u.entities.Participant;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.ParticipantService;

import java.util.ArrayList;

public class AfficherToutParticipant extends BaseForm {

    Form previous;

    public static Participant currentParticipant = null;
    Button addBtn;


    public AfficherToutParticipant(Resources res) {
        super(new BoxLayout(BoxLayout.Y_AXIS));

        Toolbar tb = new Toolbar(true);
        tb.setUIID("CustomToolbar");
        setToolbar(tb);
        getTitleArea().setUIID("Container");
        setTitle("Participant");
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


        ArrayList<Participant> listParticipants = ParticipantService.getInstance().getAll();


        if (listParticipants.size() > 0) {
            for (Participant participant : listParticipants) {
                Component model = makeParticipantModel(participant);
                this.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }

    private void addActions() {
        addBtn.addActionListener(action -> {
            currentParticipant = null;
            new AjouterParticipant(this).show();
        });

    }

    Label userLabel, eventLabel, statusLabel;


    private Container makeModelWithoutButtons(Participant participant) {
        Container participantModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        participantModel.setUIID("containerRounded");


        userLabel = new Label("User : " + participant.getUser());
        userLabel.setUIID("labelDefault");

        eventLabel = new Label("Event : " + participant.getEvent());
        eventLabel.setUIID("labelDefault");

        statusLabel = new Label("Status : " + participant.getStatus());
        statusLabel.setUIID("labelDefault");

        userLabel = new Label("User : " + participant.getUser().getEmail());
        userLabel.setUIID("labelDefault");

        eventLabel = new Label("Event : " + participant.getEvent().getTitle());
        eventLabel.setUIID("labelDefault");


        participantModel.addAll(

                userLabel, eventLabel, statusLabel
        );

        return participantModel;
    }

    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makeParticipantModel(Participant participant) {

        Container participantModel = makeModelWithoutButtons(participant);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");

        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentParticipant = participant;
            new ModifierParticipant(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce participant ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = ParticipantService.getInstance().delete(participant.getId());

                if (responseCode == 200) {
                    currentParticipant = null;
                    dlg.dispose();
                    participantModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du participant. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);


        participantModel.add(btnsContainer);

        return participantModel;
    }

}