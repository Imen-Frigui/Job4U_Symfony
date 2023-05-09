package com.job4u.gui.back.commentaire;


import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Commentaire;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.CommentaireService;

import java.text.SimpleDateFormat;
import java.util.ArrayList;

public class AfficherToutCommentaire extends BaseForm {

    Form previous; 
    
    public static Commentaire currentCommentaire = null;
    Button addBtn;

    
    

    public AfficherToutCommentaire(Form previous) {
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
        

        ArrayList<Commentaire> listCommentaires = CommentaireService.getInstance().getAll();
        
        
        if (listCommentaires.size() > 0) {
            for (Commentaire commentaire : listCommentaires) {
                Component model = makeCommentaireModel(commentaire);
                this.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }
    private void addActions() {
        addBtn.addActionListener(action -> {
            currentCommentaire = null;
            new AjouterCommentaire(this).show();
        });
        
    }
    Label posteLabel   , userLabel   , descriptionLabel   , dateLabel  ;
    

    private Container makeModelWithoutButtons(Commentaire commentaire) {
        Container commentaireModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        commentaireModel.setUIID("containerRounded");
        
        
        posteLabel = new Label("Poste : " + commentaire.getPoste());
        posteLabel.setUIID("labelDefault");
        
        userLabel = new Label("User : " + commentaire.getUser());
        userLabel.setUIID("labelDefault");
        
        descriptionLabel = new Label("Description : " + commentaire.getDescription());
        descriptionLabel.setUIID("labelDefault");
        
        dateLabel = new Label("Date : " + new SimpleDateFormat("dd-MM-yyyy").format(commentaire.getDate()));
        dateLabel.setUIID("labelDefault");
        
        posteLabel = new Label("Poste : " + commentaire.getPoste().getTitre());
        posteLabel.setUIID("labelDefault");
        
        userLabel = new Label("User : " + commentaire.getUser().getEmail());
        userLabel.setUIID("labelDefault");
        

        commentaireModel.addAll(
                
                posteLabel, userLabel, descriptionLabel, dateLabel
        );

        return commentaireModel;
    }
    
    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makeCommentaireModel(Commentaire commentaire) {

        Container commentaireModel = makeModelWithoutButtons(commentaire);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");
        
        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentCommentaire = commentaire;
            new ModifierCommentaire(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce commentaire ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = CommentaireService.getInstance().delete(commentaire.getId());

                if (responseCode == 200) {
                    currentCommentaire = null;
                    dlg.dispose();
                    commentaireModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du commentaire. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);
        
        
        commentaireModel.add(btnsContainer);

        return commentaireModel;
    }
    
}