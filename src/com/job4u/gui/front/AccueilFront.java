package com.job4u.gui.front;

import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.plaf.UIManager;
import com.codename1.ui.util.Resources;
import com.job4u.MainApp;
import com.job4u.gui.Login;

public class AccueilFront extends Form {

    Resources theme = UIManager.initFirstTheme("/theme");
    Label label;
    Form previous;
    public static Form accueilForm;

    public AccueilFront(Form previous) {
        super("Menu", new BoxLayout(BoxLayout.Y_AXIS));
        this.previous = previous;
        accueilForm = this;
        addGUIs();
    }

    private void addGUIs() {
        label = new Label("Espace client");
        label.setUIID("links");
        Button btnDeconnexion = new Button();
        btnDeconnexion.setMaterialIcon(FontImage.MATERIAL_ARROW_FORWARD);
        btnDeconnexion.addActionListener(action -> {
            Login.loginForm.showBack();
        });

        Container userContainer = new Container(new BorderLayout());
        userContainer.setUIID("userContainer");
        userContainer.add(BorderLayout.CENTER, label);
        userContainer.add(BorderLayout.EAST, btnDeconnexion);

        Container menuContainer = new Container(new BoxLayout(BoxLayout.Y_AXIS));
        menuContainer.addAll(
                userContainer,
                makeEventsButton(),
                //makeEventCategorysButton(),
                makeParticipantsButton(),
                makeNotificationsButton()


        );

        this.add(menuContainer);
    }

    private Button makeEventsButton() {
        Button button = new Button("Events");
        button.setUIID("buttonMenu");
        //button.setMaterialIcon(FontImage.MATERIAL_BOOKMARK);
        button.addActionListener(action -> new com.job4u.gui.front.event.AfficherToutEvent(MainApp.res).show());
        return button;
    }

   // private Button makeEventCategorysButton() {
     //   Button button = new Button("EventCategorys");
       // button.setUIID("buttonMenu");
        //button.setMaterialIcon(FontImage.MATERIAL_BOOKMARK);
        //button.addActionListener(action -> new com.job4u.gui.front.eventCategory.AfficherToutEventCategory(MainApp.res).show());
      //  return button;
    //}

    private Button makeParticipantsButton() {
        Button button = new Button("Participants");
        button.setUIID("buttonMenu");
        //button.setMaterialIcon(FontImage.MATERIAL_BOOKMARK);
        button.addActionListener(action -> new com.job4u.gui.front.participant.AfficherToutParticipant(MainApp.res).show());
        return button;
    }

    private Button makeNotificationsButton() {
        Button button = new Button("Notifications");
        button.setUIID("buttonMenu");
        //button.setMaterialIcon(FontImage.MATERIAL_BOOKMARK);
        button.addActionListener(action -> new com.job4u.gui.front.notification.AfficherToutNotification(MainApp.res).show());
        return button;
    }

}