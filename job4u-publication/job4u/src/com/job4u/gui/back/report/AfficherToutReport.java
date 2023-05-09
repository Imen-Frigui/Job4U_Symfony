package com.job4u.gui.back.report;


import com.codename1.components.InteractionDialog;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.job4u.entities.Report;
import com.job4u.gui.uikit.BaseForm;
import com.job4u.services.ReportService;

import java.util.ArrayList;

public class AfficherToutReport extends BaseForm {

    Form previous; 
    
    public static Report currentReport = null;
    Button addBtn;

    
    

    public AfficherToutReport(Form previous) {
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
        

        ArrayList<Report> listReports = ReportService.getInstance().getAll();
        
        
        if (listReports.size() > 0) {
            for (Report report : listReports) {
                Component model = makeReportModel(report);
                this.add(model);
            }
        } else {
            this.add(new Label("Aucune donnée"));
        }
    }
    private void addActions() {
        addBtn.addActionListener(action -> {
            currentReport = null;
            new AjouterReport(this).show();
        });
        
    }
    Label userLabel   , posteLabel   , typeLabel   , descriptionLabel  ;
    

    private Container makeModelWithoutButtons(Report report) {
        Container reportModel = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        reportModel.setUIID("containerRounded");
        
        
        userLabel = new Label("User : " + report.getUser());
        userLabel.setUIID("labelDefault");
        
        posteLabel = new Label("Poste : " + report.getPoste());
        posteLabel.setUIID("labelDefault");
        
        typeLabel = new Label("Type : " + report.getType());
        typeLabel.setUIID("labelDefault");
        
        descriptionLabel = new Label("Description : " + report.getDescription());
        descriptionLabel.setUIID("labelDefault");
        
        userLabel = new Label("User : " + report.getUser().getEmail());
        userLabel.setUIID("labelDefault");
        
        posteLabel = new Label("Poste : " + report.getPoste().getTitre());
        posteLabel.setUIID("labelDefault");
        

        reportModel.addAll(
                
                userLabel, posteLabel, typeLabel, descriptionLabel
        );

        return reportModel;
    }
    
    Button editBtn, deleteBtn;
    Container btnsContainer;

    private Component makeReportModel(Report report) {

        Container reportModel = makeModelWithoutButtons(report);

        btnsContainer = new Container(new BorderLayout());
        btnsContainer.setUIID("containerButtons");
        
        editBtn = new Button("Modifier");
        editBtn.setUIID("buttonWhiteCenter");
        editBtn.addActionListener(action -> {
            currentReport = report;
            new ModifierReport(this).show();
        });

        deleteBtn = new Button("Supprimer");
        deleteBtn.setUIID("buttonWhiteCenter");
        deleteBtn.addActionListener(action -> {
            InteractionDialog dlg = new InteractionDialog("Confirmer la suppression");
            dlg.setLayout(new BorderLayout());
            dlg.add(BorderLayout.CENTER, new Label("Voulez vous vraiment supprimer ce report ?"));
            Button btnClose = new Button("Annuler");
            btnClose.addActionListener((ee) -> dlg.dispose());
            Button btnConfirm = new Button("Confirmer");
            btnConfirm.addActionListener(actionConf -> {
                int responseCode = ReportService.getInstance().delete(report.getId());

                if (responseCode == 200) {
                    currentReport = null;
                    dlg.dispose();
                    reportModel.remove();
                    this.refreshTheme();
                } else {
                    Dialog.show("Erreur", "Erreur de suppression du report. Code d'erreur : " + responseCode, new Command("Ok"));
                }
            });
            Container btnContainer = new Container(new BoxLayout(BoxLayout.X_AXIS));
            btnContainer.addAll(btnClose, btnConfirm);
            dlg.addComponent(BorderLayout.SOUTH, btnContainer);
            dlg.show(800, 800, 0, 0);
        });

        btnsContainer.add(BorderLayout.WEST, editBtn);
        btnsContainer.add(BorderLayout.EAST, deleteBtn);
        
        
        reportModel.add(btnsContainer);

        return reportModel;
    }
    
}