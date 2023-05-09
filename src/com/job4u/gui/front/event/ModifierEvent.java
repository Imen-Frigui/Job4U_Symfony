package com.job4u.gui.front.event;


import com.codename1.capture.Capture;
import com.codename1.components.ImageViewer;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.job4u.entities.Event;
import com.job4u.entities.EventCategory;
import com.job4u.entities.User;
import com.job4u.services.EventCategoryService;
import com.job4u.services.EventService;
import com.job4u.services.UserService;
import com.job4u.utils.Statics;

import java.io.IOException;
import java.util.ArrayList;

public class ModifierEvent extends Form {


    Resources theme = UIManager.initFirstTheme("/theme");
    String selectedImage;
    boolean imageEdited = false;


    Event currentEvent;

    TextField titleTF;
    TextField descriptionTF;
    TextField locationTF;
    TextField imageTF;
    Label titleLabel;
    Label descriptionLabel;
    Label locationLabel;
    Label imageLabel;
    PickerComponent dateTF;

    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    ArrayList<EventCategory> listEventCategorys;
    PickerComponent eventCategoryPC;
    EventCategory selectedEventCategory = null;


    ImageViewer imageIV;
    Button selectImageButton;

    Button manageButton;

    Form previous;

    public ModifierEvent(Form previous) {
        super("Modifier", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        currentEvent = AfficherToutEvent.currentEvent;

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

        String[] eventCategoryStrings;
        int eventCategoryIndex;
        eventCategoryPC = PickerComponent.createStrings("").label("EventCategory");
        listEventCategorys = EventCategoryService.getInstance().getAll();
        eventCategoryStrings = new String[listEventCategorys.size()];
        eventCategoryIndex = 0;
        for (EventCategory eventCategory : listEventCategorys) {
            eventCategoryStrings[eventCategoryIndex] = eventCategory.getName();
            eventCategoryIndex++;
        }
        if (listEventCategorys.size() > 0) {
            eventCategoryPC.getPicker().setStrings(eventCategoryStrings);
            eventCategoryPC.getPicker().addActionListener(l -> selectedEventCategory = listEventCategorys.get(eventCategoryPC.getPicker().getSelectedStringIndex()));
        } else {
            eventCategoryPC.getPicker().setStrings("");
        }


        titleLabel = new Label("Title : ");
        titleLabel.setUIID("labelDefault");
        titleTF = new TextField();
        titleTF.setHint("Tapez le title");


        descriptionLabel = new Label("Description : ");
        descriptionLabel.setUIID("labelDefault");
        descriptionTF = new TextField();
        descriptionTF.setHint("Tapez le description");


        dateTF = PickerComponent.createDate(null).label("Date");


        locationLabel = new Label("Location : ");
        locationLabel.setUIID("labelDefault");
        locationTF = new TextField();
        locationTF.setHint("Tapez le location");


        imageLabel = new Label("Image : ");
        imageLabel.setUIID("labelDefault");
        selectImageButton = new Button("Ajouter une image");


        titleTF.setText(currentEvent.getTitle());
        descriptionTF.setText(currentEvent.getDescription());
        dateTF.getPicker().setDate(currentEvent.getDate());
        locationTF.setText(currentEvent.getLocation());


        userPC.getPicker().setSelectedString(currentEvent.getUser().getEmail());
        selectedUser = currentEvent.getUser();
        eventCategoryPC.getPicker().setSelectedString(currentEvent.getEventCategory().getName());
        selectedEventCategory = currentEvent.getEventCategory();


        if (currentEvent.getImage() != null) {
            selectedImage = currentEvent.getImage();
            String url = Statics.EVENT_IMAGE_URL + currentEvent.getImage();
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


        manageButton = new Button("Modifier");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(
                imageLabel, imageIV,
                selectImageButton,


                titleLabel, titleTF,
                descriptionLabel, descriptionTF,
                dateTF,
                locationLabel, locationTF,

                userPC, eventCategoryPC,
                manageButton
        );

        this.addAll(container);
    }

    private void addActions() {

        selectImageButton.addActionListener(a -> {
            selectedImage = Capture.capturePhoto(900, -1);
            try {
                imageEdited = true;
                imageIV.setImage(Image.createImage(selectedImage));
            } catch (IOException e) {
                e.printStackTrace();
            }
            selectImageButton.setText("Modifier l'image");
        });

        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = EventService.getInstance().edit(
                        new Event(
                                currentEvent.getId(),


                                selectedUser,
                                selectedEventCategory,
                                titleTF.getText(),
                                descriptionTF.getText(),
                                dateTF.getPicker().getDate(),
                                locationTF.getText(),
                                selectedImage

                        ), imageEdited
                );
                if (responseCode == 200) {
                    Dialog.show("Succés", "Event modifié avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur de modification de event. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh() {
        ((AfficherToutEvent) previous).refresh();
        previous.showBack();
    }

    private boolean controleDeSaisie() {


        if (titleTF.getText().equals("")) {
            Dialog.show("Avertissement", "Title vide", new Command("Ok"));
            return false;
        }


        if (descriptionTF.getText().equals("")) {
            Dialog.show("Avertissement", "Description vide", new Command("Ok"));
            return false;
        }


        if (dateTF.getPicker().getDate() == null) {
            Dialog.show("Avertissement", "Veuillez saisir la date", new Command("Ok"));
            return false;
        }


        if (locationTF.getText().equals("")) {
            Dialog.show("Avertissement", "Location vide", new Command("Ok"));
            return false;
        }


        if (selectedUser == null) {
            Dialog.show("Avertissement", "Veuillez choisir un user", new Command("Ok"));
            return false;
        }

        if (selectedEventCategory == null) {
            Dialog.show("Avertissement", "Veuillez choisir un eventCategory", new Command("Ok"));
            return false;
        }


        if (selectedImage == null) {
            Dialog.show("Avertissement", "Veuillez choisir une image", new Command("Ok"));
            return false;
        }


        return true;
    }
}