package com.job4u.gui.front.eventCategory;


import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.util.Resources;
import com.job4u.entities.EventCategory;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.EventCategoryService;

import java.util.ArrayList;

public class AfficherToutEventCategory extends BaseForm {

    Form previous;

    public static EventCategory currentEventCategory = null;
    Button addBtn;

    TextField searchTF;
    ArrayList<Component> componentModels;


    public AfficherToutEventCategory(Resources res) {
        super(new BoxLayout(BoxLayout.Y_AXIS));

        Toolbar tb = new Toolbar(true);
        tb.setUIID("CustomToolbar");
        setToolbar(tb);
        getTitleArea().setUIID("Container");
        setTitle("EventCategory");
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


        ArrayList<EventCategory> listEventCategorys = EventCategoryService.getInstance().getAll();
        componentModels = new ArrayList<>();

        searchTF = new TextField("", "Chercher eventCategory par Name");
        searchTF.addDataChangedListener((d, t) -> {
            if (componentModels.size() > 0) {
                for (Component componentModel : componentModels) {
                    this.removeComponent(componentModel);
                }
            }
            componentModels = new ArrayList<>();
            for (EventCategory eventCategory : listEventCategorys) {
                if (eventCategory.getName().toLowerCase().startsWith(searchTF.getText().toLowerCase())) {
                    Component model = makeEventCategoryModel(eventCategory);
                    this.add(model);
                    componentModels.add(model);
                }
            }
            this.revalidate();
        });
        this.add(searchTF);


        if (listEventCategorys.size() > 0) {
            for (EventCategory eventCategory : listEventCategorys) {
                Component model = makeEventCategoryModel(eventCategory);
                this.add(model);
                componentModels.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }

    private void addActions() {
        addBtn.addActionListener(action -> {
            currentEventCategory = null;
            new AjouterEventCategory(this).show();
        });

    }

    Label descriptionLabel, nameLabel;


    private Container makeModelWithoutButtons(EventCategory eventCategory) {
        Container eventCategoryModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        eventCategoryModel.setUIID("containerRounded");


        descriptionLabel = new Label("Description : " + eventCategory.getDescription());
        descriptionLabel.setUIID("labelDefault");

        nameLabel = new Label("Name : " + eventCategory.getName());
        nameLabel.setUIID("labelDefault");


        eventCategoryModel.addAll(

                descriptionLabel, nameLabel
        );

        return eventCategoryModel;
    }

    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makeEventCategoryModel(EventCategory eventCategory) {

        Container eventCategoryModel = makeModelWithoutButtons(eventCategory);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");

        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentEventCategory = eventCategory;
            new ModifierEventCategory(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce eventCategory ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = EventCategoryService.getInstance().delete(eventCategory.getId());

                if (responseCode == 200) {
                    currentEventCategory = null;
                    dlg.dispose();
                    eventCategoryModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du eventCategory. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);


        eventCategoryModel.add(btnsContainer);

        return eventCategoryModel;
    }

}