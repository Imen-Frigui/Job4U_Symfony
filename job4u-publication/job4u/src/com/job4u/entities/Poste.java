package com.job4u.entities;

import java.util.Date;
import com.job4u.utils.*;

public class Poste {

    private int id;
    private User user;
    private String titre;
    private String description;
    private String image;
    private String domaine;
    private Date date;

    public Poste() {}

    public Poste(int id, User user, String titre, String description, String image, String domaine, Date date) {
        this.id = id;
        this.user = user;
        this.titre = titre;
        this.description = description;
        this.image = image;
        this.domaine = domaine;
        this.date = date;
    }

    public Poste(User user, String titre, String description, String image, String domaine, Date date) {
        this.user = user;
        this.titre = titre;
        this.description = description;
        this.image = image;
        this.domaine = domaine;
        this.date = date;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public String getTitre() {
        return titre;
    }

    public void setTitre(String titre) {
        this.titre = titre;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public String getDomaine() {
        return domaine;
    }

    public void setDomaine(String domaine) {
        this.domaine = domaine;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    @Override
    public String toString() {
        return titre;
    }

}