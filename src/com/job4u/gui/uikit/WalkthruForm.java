package com.job4u.gui.uikit;

import com.codename1.components.ScaleImageLabel;
import com.codename1.components.SpanLabel;
import com.codename1.ui.*;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.layouts.FlowLayout;
import com.codename1.ui.layouts.LayeredLayout;
import com.codename1.ui.util.Resources;

/**
 * Swiping thru tutorial
 *
 * @author Shai Almog
 */
public class WalkthruForm extends Form {

    public WalkthruForm(Resources res) {
        super(new BorderLayout());
        getTitleArea().setUIID("Container");
        setUIID("Welcome");
        Tabs t = new Tabs();
        t.hideTabs();
        t.setUIID("Container");
        t.getContentPane().setUIID("Container");
        add(BorderLayout.CENTER, t);

        ScaleImageLabel page1 = new ScaleImageLabel(res.getImage("welcome-slide-1.png"));
        ScaleImageLabel page2 = new ScaleImageLabel(res.getImage("welcome-slide-2.png"));
        ScaleImageLabel page3 = new ScaleImageLabel(res.getImage("welcome-slide-3.png"));
        page1.setUIID("Container");
        page2.setUIID("Container");
        page3.setUIID("Container");
        page1.getAllStyles().setBgTransparency(0);
        page2.getAllStyles().setBgTransparency(0);
        page3.getAllStyles().setBgTransparency(0);
        t.addTab("", page1);
        t.addTab("", page2);
        t.addTab("", page3);

        String[] messages = {
                "Manage your tasks quickly\nand efficiently",
                "This demo is powered by\nCodename One",
                "Start NOW\n press skip"
        };

        SpanLabel message = new SpanLabel(messages[0], "WelcomeMessage");


        ButtonGroup bg = new ButtonGroup();
        int size = Display.getInstance().convertToPixels(1);
        Image unselectedWalkthru = Image.createImage(size, size, 0);
        Graphics g = unselectedWalkthru.getGraphics();
        g.setColor(0xcccccc);
        g.setAntiAliased(true);
        g.fillArc(0, 0, size, size, 0, 360);
        Image selectedWalkthru = Image.createImage(size, size, 0);
        g = selectedWalkthru.getGraphics();
        g.setColor(0xff2d55);
        g.setAntiAliased(true);
        g.fillArc(0, 0, size, size, 0, 360);
        RadioButton[] rbs = new RadioButton[t.getTabCount()];
        FlowLayout flow = new FlowLayout(CENTER);
        flow.setValign(CENTER);
        Container radioContainer = new Container(flow);
        for (int iter = 0; iter < rbs.length; iter++) {
            rbs[iter] = RadioButton.createToggle(unselectedWalkthru, bg);
            rbs[iter].setPressedIcon(selectedWalkthru);
            rbs[iter].setUIID("Label");
            radioContainer.add(rbs[iter]);
        }

        rbs[0].setSelected(true);
        t.addSelectionListener((i, ii) -> {
            if (!rbs[ii].isSelected()) {
                rbs[ii].setSelected(true);
                message.setText(messages[ii]);
            }
        });

        Button skip = new Button("Skip");
        skip.setUIID("SkipButton");
        skip.addActionListener(e -> new SignInForm(res).show());

        Container welcomeNoteArea = BoxLayout.encloseY(message,
                LayeredLayout.encloseIn(
                        radioContainer,
                        BorderLayout.east(skip)
                )
        );
        welcomeNoteArea.setUIID("WelcomeNoteArea");
        add(BorderLayout.SOUTH, welcomeNoteArea);
    }

}
