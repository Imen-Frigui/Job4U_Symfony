package com.job4u.gui.back.poste;


import com.codename1.capture.Capture;
import com.codename1.components.ImageViewer;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.job4u.entities.Poste;
import com.job4u.entities.User;
import com.job4u.services.PosteService;
import com.job4u.services.UserService;

import java.io.IOException;
import java.util.ArrayList;

public class AjouterPoste extends Form {

    
    Resources theme = UIManager.initFirstTheme("/theme");
    String selectedImage;
    boolean imageEdited = false;
    

    TextField titreTF;TextField descriptionTF;TextField imageTF;TextField domaineTF;
    Label titreLabel;Label descriptionLabel;Label imageLabel;Label domaineLabel;
    PickerComponent dateTF;
    
    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    
    
    ImageViewer imageIV;
    Button selectImageButton;
    
    Button manageButton;

    Form previous;

    public AjouterPoste(Form previous) {
        super("Ajouter", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        addGUIs();
        addActions();

        
        getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e ->  previous.showBack());
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
        if(listUsers.size() > 0) {
            userPC.getPicker().setStrings(userStrings);
            userPC.getPicker().addActionListener(l -> selectedUser = listUsers.get(userPC.getPicker().getSelectedStringIndex()));
        } else {
            userPC.getPicker().setStrings("");
        }
        

        
        
        
        
        titreLabel = new Label("Titre : ");
        titreLabel.setUIID("labelDefault");
        titreTF = new TextField();
        titreTF.setHint("Tapez le titre");
        
        
        
        descriptionLabel = new Label("Description : ");
        descriptionLabel.setUIID("labelDefault");
        descriptionTF = new TextField();
        descriptionTF.setHint("Tapez le description");
        
        
        
        
        
        
        
        domaineLabel = new Label("Domaine : ");
        domaineLabel.setUIID("labelDefault");
        domaineTF = new TextField();
        domaineTF.setHint("Tapez le domaine");
        
        
        dateTF = PickerComponent.createDate(null).label("Date");
        
        
        
        imageLabel = new Label("Image : ");
        imageLabel.setUIID("labelDefault");
        selectImageButton = new Button("Ajouter une image");

        
        imageIV = new ImageViewer(theme.getImage("profile-pic.jpg").fill(1100, 500));
        

        manageButton = new Button("Ajouter");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(
            imageLabel, imageIV,
            selectImageButton,
            
            titreLabel, titreTF,
            descriptionLabel, descriptionTF,
            
            domaineLabel, domaineTF,
            dateTF,
            userPC,
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
                int responseCode = PosteService.getInstance().add(
                        new Poste(
                                
                                
                                selectedUser,
                                titreTF.getText(),
                                descriptionTF.getText(),
                                selectedImage,
                                domaineTF.getText(),
                                dateTF.getPicker().getDate()
                        )
                );
                if (responseCode == 200) {
                     Dialog.show("Succés", "Poste ajouté avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur d'ajout de poste. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh(){
        ((AfficherToutPoste) previous).refresh();
         previous.showBack();
    }

    private boolean controleDeSaisie() {

        
        
        
        
        
        if (titreTF.getText().equals("")) {
            Dialog.show("Avertissement", "Titre vide", new Command("Ok"));
            return false;
        }
        
        
        
        
        if (descriptionTF.getText().equals("")) {
            Dialog.show("Avertissement", "Description vide", new Command("Ok"));
            return false;
        }
        
        
        
        
        
        
        
        if (domaineTF.getText().equals("")) {
            Dialog.show("Avertissement", "Domaine vide", new Command("Ok"));
            return false;
        }
        
        
        
        
        
        
        if (dateTF.getPicker().getDate() == null) {
            Dialog.show("Avertissement", "Veuillez saisir la date", new Command("Ok"));
            return false;
        }
        

        
        if (selectedUser == null) {
            Dialog.show("Avertissement", "Veuillez choisir un user", new Command("Ok"));
            return false;
        }
        

        
        if (selectedImage == null) {
            Dialog.show("Avertissement", "Veuillez choisir une image", new Command("Ok"));
            return false;
        }
        
             
        return true;
    }
}