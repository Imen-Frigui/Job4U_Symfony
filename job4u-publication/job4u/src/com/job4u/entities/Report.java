package com.job4u.entities;

public class Report {

    private int id;
    private User user;
    private Poste poste;
    private String type;
    private String description;

    public Report() {
    }

    public Report(int id, User user, Poste poste, String type, String description) {
        this.id = id;
        this.user = user;
        this.poste = poste;
        this.type = type;
        this.description = description;
    }

    public Report(User user, Poste poste, String type, String description) {
        this.user = user;
        this.poste = poste;
        this.type = type;
        this.description = description;
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

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }


}