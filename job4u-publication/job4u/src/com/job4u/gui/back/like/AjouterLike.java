package com.job4u.gui.back.like;


import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Like;
import com.job4u.entities.Poste;
import com.job4u.entities.User;
import com.job4u.services.LikeService;
import com.job4u.services.PosteService;
import com.job4u.services.UserService;

import java.util.ArrayList;

public class AjouterLike extends Form {

    

    
    
    
    
    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    ArrayList<Poste> listPostes;
    PickerComponent postePC;
    Poste selectedPoste = null;
    
    
    Button manageButton;

    Form previous;

    public AjouterLike(Form previous) {
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
        

        
        
        
        
        
        

        

        manageButton = new Button("Ajouter");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(
            
            
            
            userPC,postePC,
            manageButton
        );

        this.addAll(container);
    }

    private void addActions() {
        
        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = LikeService.getInstance().add(
                        new Like(
                                
                                
                                selectedUser,
                                selectedPoste
                        )
                );
                if (responseCode == 200) {
                     Dialog.show("Succés", "Like ajouté avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur d'ajout de like. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh(){
        ((AfficherToutLike) previous).refresh();
         previous.showBack();
    }

    private boolean controleDeSaisie() {

        
        
        
        
        
        
        

        
        if (selectedUser == null) {
            Dialog.show("Avertissement", "Veuillez choisir un user", new Command("Ok"));
            return false;
        }
        
        if (selectedPoste == null) {
            Dialog.show("Avertissement", "Veuillez choisir un poste", new Command("Ok"));
            return false;
        }
        

        
             
        return true;
    }
}