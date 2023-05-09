package com.job4u.gui.back.event;


import com.codename1.components.ImageViewer;
import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.job4u.entities.Event;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.EventService;
import com.job4u.utils.Statics;

import java.text.SimpleDateFormat;
import java.util.ArrayList;

public class AfficherToutEvent extends BaseForm {

    Form previous;

    Resources theme = UIManager.initFirstTheme("/theme");

    public static Event currentEvent = null;
    Button addBtn;

    TextField searchTF;
    ArrayList<Component> componentModels;
    Calendar calendar;


    public AfficherToutEvent(Form previous) {
        super(new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        addGUIs();
        addActions();

        super.getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e -> previous.showBack());
    }

    public void refresh() {
        this.removeAll();
        addGUIs();
        addActions();
        this.refreshTheme();
    }

    private void addGUIs() {


        addBtn = new Button("Ajouter");
        addBtn.setUIID("buttonWhiteCenter");
        this.add(addBtn);


        ArrayList<Event> listEvents = EventService.getInstance().getAll();
        componentModels = new ArrayList<>();

        searchTF = new TextField("", "Chercher event par Title");
        searchTF.addDataChangedListener((d, t) -> {
            if (componentModels.size() > 0) {
                for (Component componentModel : componentModels) {
                    this.removeComponent(componentModel);
                }
            }
            componentModels = new ArrayList<>();
            for (Event event : listEvents) {
                if (event.getTitle().toLowerCase().startsWith(searchTF.getText().toLowerCase())) {
                    Component model = makeEventModel(event);
                    this.add(model);
                    componentModels.add(model);
                }
            }
            this.revalidate();
        });
        this.add(searchTF);


        if (listEvents.size() > 0) {
            for (Event event : listEvents) {
                Component model = makeEventModel(event);
                this.add(model);
                componentModels.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }

    private void addActions() {
        addBtn.addActionListener(action -> {
            currentEvent = null;
            new AjouterEvent(this).show();
        });

    }

    Label userLabel, eventCategoryLabel, titleLabel, descriptionLabel, dateLabel, locationLabel, imageLabel;

    ImageViewer imageIV;


    private Container makeModelWithoutButtons(Event event) {
        Container eventModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        eventModel.setUIID("containerRounded");


        userLabel = new Label("User : " + event.getUser());
        userLabel.setUIID("labelDefault");

        eventCategoryLabel = new Label("EventCategory : " + event.getEventCategory());
        eventCategoryLabel.setUIID("labelDefault");

        titleLabel = new Label("Title : " + event.getTitle());
        titleLabel.setUIID("labelDefault");

        descriptionLabel = new Label("Description : " + event.getDescription());
        descriptionLabel.setUIID("labelDefault");

        dateLabel = new Label("Date : " + new SimpleDateFormat("dd-MM-yyyy").format(event.getDate()));
        dateLabel.setUIID("labelDefault");

        locationLabel = new Label("Location : " + event.getLocation());
        locationLabel.setUIID("labelDefault");

        imageLabel = new Label("Image : " + event.getImage());
        imageLabel.setUIID("labelDefault");

        userLabel = new Label("User : " + event.getUser().getEmail());
        userLabel.setUIID("labelDefault");

        eventCategoryLabel = new Label("EventCategory : " + event.getEventCategory().getName());
        eventCategoryLabel.setUIID("labelDefault");

        if (event.getDate() != null) {
            calendar = new Calendar();
            calendar.setFocusable(false);
            calendar.setDate(event.getDate());
            calendar.highlightDate(event.getDate(), "dateStart");
        }

        if (event.getImage() != null) {
            String url = Statics.EVENT_IMAGE_URL + event.getImage();
            Image image = URLImage.createToStorage(
                    EncodedImage.createFromImage(theme.getImage("profile-pic.jpg").fill(1100, 500), false),
                    url,
                    url,
                    URLImage.RESIZE_SCALE
            );
            imageIV = new ImageViewer(image);
        } else {
            imageIV = new ImageViewer(theme.getImage("profile-pic.jpg").fill(1100, 500));
        }
        imageIV.setFocusable(false);

        eventModel.addAll(
                imageIV,
                userLabel, eventCategoryLabel, titleLabel, descriptionLabel, dateLabel, locationLabel
        );

        if (event.getDate() != null) {
            eventModel.add(calendar);
        }

        return eventModel;
    }

    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makeEventModel(Event event) {

        Container eventModel = makeModelWithoutButtons(event);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");

        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentEvent = event;
            new ModifierEvent(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce event ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = EventService.getInstance().delete(event.getId());

                if (responseCode == 200) {
                    currentEvent = null;
                    dlg.dispose();
                    eventModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du event. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);


        eventModel.add(btnsContainer);

        return eventModel;
    }

}