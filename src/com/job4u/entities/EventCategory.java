package com.job4u.entities;

public class EventCategory {

    private int id;
    private String description;
    private String name;

    public EventCategory() {
    }

    public EventCategory(int id, String description, String name) {
        this.id = id;
        this.description = description;
        this.name = name;
    }

    public EventCategory(String description, String name) {
        this.description = description;
        this.name = name;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }


}