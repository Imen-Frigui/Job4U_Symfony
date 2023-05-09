package com.job4u.entities;

public class Like {

    private int id;
    private User user;
    private Poste poste;

    public Like() {
    }

    public Like(int id, User user, Poste poste) {
        this.id = id;
        this.user = user;
        this.poste = poste;
    }

    public Like(User user, Poste poste) {
        this.user = user;
        this.poste = poste;
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

    public Poste getPoste() {
        return poste;
    }

    public void setPoste(Poste poste) {
        this.poste = poste;
    }


}