package com.job4u.gui.back.commentaire;


import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Commentaire;
import com.job4u.entities.Poste;
import com.job4u.entities.User;
import com.job4u.services.CommentaireService;
import com.job4u.services.PosteService;
import com.job4u.services.UserService;

import java.util.ArrayList;

public class ModifierCommentaire extends Form {

    

    Commentaire currentCommentaire;

    TextField descriptionTF;
    Label descriptionLabel;
    PickerComponent dateTF;
    
    ArrayList<Poste> listPostes;
    PickerComponent postePC;
    Poste selectedPoste = null;
    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    
    
    Button manageButton;

    Form previous;

    public ModifierCommentaire(Form previous) {
        super("Modifier", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;

        currentCommentaire = AfficherToutCommentaire.currentCommentaire;

        addGUIs();
        addActions();

        
        getToolbar().addMaterialCommandToLeftBar("  ", FontImage.MATERIAL_ARROW_BACK, e ->  previous.showBack());
    }

    private void addGUIs() {
        
        String[] posteStrings;
        int posteIndex;
        postePC = PickerComponent.createStrings("").label("Poste");
        listPostes = PosteService.getInstance().getAll();
        posteStrings = new String[listPostes.size()];
        posteIndex = 0;
        for (Poste poste : listPostes) {
            posteStrings[posteIndex] = poste.getTitre();
            posteIndex++;
        }
        if(listPostes.size() > 0) {
            postePC.getPicker().setStrings(posteStrings);
            postePC.getPicker().addActionListener(l -> selectedPoste = listPostes.get(postePC.getPicker().getSelectedStringIndex()));
        } else {
            postePC.getPicker().setStrings("");
        }
        
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
        

        
        
        
        
        
        
        descriptionLabel = new Label("Description : ");
        descriptionLabel.setUIID("labelDefault");
        descriptionTF = new TextField();
        descriptionTF.setHint("Tapez le description");
        
        
        dateTF = PickerComponent.createDate(null).label("Date");
        
        
        

        
        
        descriptionTF.setText(currentCommentaire.getDescription());
        dateTF.getPicker().setDate(currentCommentaire.getDate());
        
        postePC.getPicker().setSelectedString(currentCommentaire.getPoste().getTitre());
        selectedPoste = currentCommentaire.getPoste();
        userPC.getPicker().setSelectedString(currentCommentaire.getUser().getEmail());
        selectedUser = currentCommentaire.getUser();
        
        

        manageButton = new Button("Modifier");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(
            
            
            
            descriptionLabel, descriptionTF,
            dateTF,
            postePC,userPC,
            manageButton
        );

        this.addAll(container);
    }

    private void addActions() {
        
        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = CommentaireService.getInstance().edit(
                        new Commentaire(
                                currentCommentaire.getId(),
                                
                                
                                selectedPoste,
                                selectedUser,
                                descriptionTF.getText(),
                                dateTF.getPicker().getDate()

                        )
                );
                if (responseCode == 200) {
                     Dialog.show("Succés", "Commentaire modifié avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur de modification de commentaire. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh(){
        ((AfficherToutCommentaire) previous).refresh();
         previous.showBack();
    }

    private boolean controleDeSaisie() {

        
        
        
        
        
        
        
        
        if (descriptionTF.getText().equals("")) {
            Dialog.show("Avertissement", "Description vide", new Command("Ok"));
            return false;
        }
        
        
        
        
        
        
        if (dateTF.getPicker().getDate() == null) {
            Dialog.show("Avertissement", "Veuillez saisir la date", new Command("Ok"));
            return false;
        }
        

        
        if (selectedPoste == null) {
            Dialog.show("Avertissement", "Veuillez choisir un poste", new Command("Ok"));
            return false;
        }
        
        if (selectedUser == null) {
            Dialog.show("Avertissement", "Veuillez choisir un user", new Command("Ok"));
            return false;
        }
        

        
             
        return true;
    }
}