package com.job4u.gui.uikit;

import com.codename1.components.ScaleImageLabel;
import com.codename1.ui.*;
import com.codename1.ui.layouts.FlowLayout;
import com.codename1.ui.layouts.LayeredLayout;
import com.codename1.ui.layouts.Layout;
import com.codename1.ui.plaf.Style;
import com.codename1.ui.util.Resources;
import com.job4u.gui.Login;
import com.job4u.gui.front.event.AfficherToutEvent;
import com.job4u.gui.front.eventCategory.AfficherToutEventCategory;
import com.job4u.gui.front.notification.AfficherToutNotification;
import com.job4u.gui.front.participant.AfficherToutParticipant;


/**
 * Base class for the forms with common functionality
 *
 * @author Shai Almog
 */
public class BaseForm extends Form {

    public BaseForm() {
    }

    public BaseForm(Layout contentPaneLayout) {
        super(contentPaneLayout);
    }

    public BaseForm(String title, Layout contentPaneLayout) {
        super(title, contentPaneLayout);
    }


    public Component createLineSeparator() {
        Label separator = new Label("", "WhiteSeparator");
        separator.setShowEvenIfBlank(true);
        return separator;
    }

    public Component createLineSeparator(int color) {
        Label separator = new Label("", "WhiteSeparator");
        separator.getUnselectedStyle().setBgColor(color);
        separator.getUnselectedStyle().setBgTransparency(255);
        separator.setShowEvenIfBlank(true);
        return separator;
    }

    protected void addSideMenu(Resources res) {
        Toolbar tb = getToolbar();
        tb.setUIID("CustomToolbar");

        Image img = res.getImage("profile-background.jpg");
        if (img.getHeight() > Display.getInstance().getDisplayHeight() / 3) {
            img = img.scaledHeight(Display.getInstance().getDisplayHeight() / 3);
        }
        ScaleImageLabel sl = new ScaleImageLabel(img);
        sl.setUIID("BottomPad");
        sl.setBackgroundType(Style.BACKGROUND_IMAGE_SCALED_FILL);

        tb.addComponentToSideMenu(LayeredLayout.encloseIn(
                sl,
                FlowLayout.encloseCenterBottom(
                        new Label(res.getImage("profile-pic.jpg"), "PictureWhiteBackgrond"))
        ));

        tb.addMaterialCommandToSideMenu("Profile", FontImage.MATERIAL_SETTINGS, e -> new ProfileForm(res).show());
        tb.addMaterialCommandToSideMenu("Events", FontImage.MATERIAL_EVENT, e -> new AfficherToutEvent(res).show());
        tb.addMaterialCommandToSideMenu("Event categories", FontImage.MATERIAL_CATEGORY, e -> new AfficherToutEventCategory(res).show());
        tb.addMaterialCommandToSideMenu("Participants", FontImage.MATERIAL_PERSON, e -> new AfficherToutParticipant(res).show());
        tb.addMaterialCommandToSideMenu("Notifications", FontImage.MATERIAL_NOTIFICATIONS, e -> new AfficherToutNotification(res).show());


        tb.addMaterialCommandToSideMenu("Logout", FontImage.MATERIAL_EXIT_TO_APP, e -> new Login().show());
    }
}
