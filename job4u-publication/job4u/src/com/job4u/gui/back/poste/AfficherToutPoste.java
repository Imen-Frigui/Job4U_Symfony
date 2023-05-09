package com.job4u.gui.back.poste;


import com.codename1.components.ImageViewer;
import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.job4u.entities.Poste;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.PosteService;
import com.job4u.utils.Statics;

import java.text.SimpleDateFormat;
import java.util.ArrayList;

public class AfficherToutPoste extends BaseForm {

    Form previous; 
    
    Resources theme = UIManager.initFirstTheme("/theme");
    
    public static Poste currentPoste = null;
    Button addBtn;

    
    

    public AfficherToutPoste(Form previous) {
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
        

        ArrayList<Poste> listPostes = PosteService.getInstance().getAll();
        
        
        if (listPostes.size() > 0) {
            for (Poste poste : listPostes) {
                Component model = makePosteModel(poste);
                this.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }
    private void addActions() {
        addBtn.addActionListener(action -> {
            currentPoste = null;
            new AjouterPoste(this).show();
        });
        
    }
    Label userLabel   , titreLabel   , descriptionLabel   , imageLabel   , domaineLabel   , dateLabel  ;
    
    ImageViewer imageIV;
    

    private Container makeModelWithoutButtons(Poste poste) {
        Container posteModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        posteModel.setUIID("containerRounded");
        
        
        userLabel = new Label("User : " + poste.getUser());
        userLabel.setUIID("labelDefault");
        
        titreLabel = new Label("Titre : " + poste.getTitre());
        titreLabel.setUIID("labelDefault");
        
        descriptionLabel = new Label("Description : " + poste.getDescription());
        descriptionLabel.setUIID("labelDefault");
        
        imageLabel = new Label("Image : " + poste.getImage());
        imageLabel.setUIID("labelDefault");
        
        domaineLabel = new Label("Domaine : " + poste.getDomaine());
        domaineLabel.setUIID("labelDefault");
        
        dateLabel = new Label("Date : " + new SimpleDateFormat("dd-MM-yyyy").format(poste.getDate()));
        dateLabel.setUIID("labelDefault");
        
        userLabel = new Label("User : " + poste.getUser().getEmail());
        userLabel.setUIID("labelDefault");
        
        if (poste.getImage() != null) {
            String url = Statics.POSTE_IMAGE_URL + poste.getImage();
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

        posteModel.addAll(
                imageIV,
                userLabel, titreLabel, descriptionLabel, domaineLabel,dateLabel
        );

        return posteModel;
    }
    
    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makePosteModel(Poste poste) {

        Container posteModel = makeModelWithoutButtons(poste);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");
        
        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentPoste = poste;
            new ModifierPoste(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce poste ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = PosteService.getInstance().delete(poste.getId());

                if (responseCode == 200) {
                    currentPoste = null;
                    dlg.dispose();
                    posteModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du poste. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);
        
        
        posteModel.add(btnsContainer);

        return posteModel;
    }
    
}