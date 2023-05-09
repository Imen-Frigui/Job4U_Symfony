package com.job4u.entities;

import java.util.Date;
import com.job4u.utils.*;

public class Commentaire {

    private int id;
    private Poste poste;
    private User user;
    private String description;
    private Date date;

    public Commentaire() {}

    public Commentaire(int id, Poste poste, User user, String description, Date date) {
        this.id = id;
        this.poste = poste;
        this.user = user;
        this.description = description;
        this.date = date;
    }

    public Commentaire(Poste poste, User user, String description, Date date) {
        this.poste = poste;
        this.user = user;
        this.description = description;
        this.date = date;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Poste getPoste() {
        return poste;
    }

    public void setPoste(Poste poste) {
        this.poste = poste;
    }

    public User getUser() {
        return user;
    }

    public void setUser(User user) {
        this.user = user;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    @Override
    public String toString() {
        return description;
    }
}