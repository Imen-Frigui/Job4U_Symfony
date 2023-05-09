package com.job4u.gui.back.eventCategory;


import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.EventCategory;
import com.job4u.services.EventCategoryService;

public class AjouterEventCategory extends Form {


    TextField descriptionTF;
    TextField nameTF;
    Label descriptionLabel;
    Label nameLabel;


    Button manageButton;

    Form previous;

    public AjouterEventCategory(Form previous) {
        super("Ajouter", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        addGUIs();
        addActions();


        getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e -> previous.showBack());
    }

    private void addGUIs() {


        descriptionLabel = new Label("Description : ");
        descriptionLabel.setUIID("labelDefault");
        descriptionTF = new TextField();
        descriptionTF.setHint("Tapez le description");


        nameLabel = new Label("Name : ");
        nameLabel.setUIID("labelDefault");
        nameTF = new TextField();
        nameTF.setHint("Tapez le name");


        manageButton = new Button("Ajouter");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(

                descriptionLabel, descriptionTF,
                nameLabel, nameTF,

                manageButton
        );

        this.addAll(container);
    }

    private void addActions() {

        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = EventCategoryService.getInstance().add(
                        new EventCategory(


                                descriptionTF.getText(),
                                nameTF.getText()
                        )
                );
                if (responseCode == 200) {
                    Dialog.show("Succés", "EventCategory ajouté avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur d'ajout de eventCategory. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh() {
        ((AfficherToutEventCategory) previous).refresh();
        previous.showBack();
    }

    private boolean controleDeSaisie() {


        if (descriptionTF.getText().equals("")) {
            Dialog.show("Avertissement", "Description vide", new Command("Ok"));
            return false;
        }


        if (nameTF.getText().equals("")) {
            Dialog.show("Avertissement", "Name vide", new Command("Ok"));
            return false;
        }


        return true;
    }
}