package com.job4u.gui.back.report;


import com.codename1.ui.*;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Poste;
import com.job4u.entities.Report;
import com.job4u.entities.User;
import com.job4u.services.PosteService;
import com.job4u.services.ReportService;
import com.job4u.services.UserService;

import java.util.ArrayList;

public class AjouterReport extends Form {

    

    TextField typeTF;TextField descriptionTF;
    Label typeLabel;Label descriptionLabel;
    
    
    ArrayList<User> listUsers;
    PickerComponent userPC;
    User selectedUser = null;
    ArrayList<Poste> listPostes;
    PickerComponent postePC;
    Poste selectedPoste = null;
    
    
    Button manageButton;

    Form previous;

    public AjouterReport(Form previous) {
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
        

        
        
        
        
        
        
        typeLabel = new Label("Type : ");
        typeLabel.setUIID("labelDefault");
        typeTF = new TextField();
        typeTF.setHint("Tapez le type");
        
        
        
        descriptionLabel = new Label("Description : ");
        descriptionLabel.setUIID("labelDefault");
        descriptionTF = new TextField();
        descriptionTF.setHint("Tapez le description");
        
        
        

        

        manageButton = new Button("Ajouter");
        manageButton.setUIID("buttonWhiteCenter");

        Container container = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        container.setUIID("containerRounded");

        container.addAll(
            
            
            
            typeLabel, typeTF,
            descriptionLabel, descriptionTF,
            userPC,postePC,
            manageButton
        );

        this.addAll(container);
    }

    private void addActions() {
        
        manageButton.addActionListener(action -> {
            if (controleDeSaisie()) {
                int responseCode = ReportService.getInstance().add(
                        new Report(
                                
                                
                                selectedUser,
                                selectedPoste,
                                typeTF.getText(),
                                descriptionTF.getText()
                        )
                );
                if (responseCode == 200) {
                     Dialog.show("Succés", "Report ajouté avec succes", new Command("Ok"));
                    showBackAndRefresh();
                } else {
                    Dialog.show("Erreur", "Erreur d'ajout de report. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            }
        });
    }

    private void showBackAndRefresh(){
        ((AfficherToutReport) previous).refresh();
         previous.showBack();
    }

    private boolean controleDeSaisie() {

        
        
        
        
        
        
        
        
        if (typeTF.getText().equals("")) {
            Dialog.show("Avertissement", "Type vide", new Command("Ok"));
            return false;
        }
        
        
        
        
        if (descriptionTF.getText().equals("")) {
            Dialog.show("Avertissement", "Description vide", new Command("Ok"));
            return false;
        }
        
        
        

        
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