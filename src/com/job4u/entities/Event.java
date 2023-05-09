package com.job4u.entities;

import java.util.Date;

public class Event {

    private int id;
    private User user;
    private EventCategory eventCategory;
    private String title;
    private String description;
    private Date date;
    private String location;
    private String image;

    public Event() {
    }

    public Event(int id, User user, EventCategory eventCategory, String title, String description, Date date, String location, String image) {
        this.id = id;
        this.user = user;
        this.eventCategory = eventCategory;
        this.title = title;
        this.description = description;
        this.date = date;
        this.location = location;
        this.image = image;
    }

    public Event(User user, EventCategory eventCategory, String title, String description, Date date, String location, String image) {
        this.user = user;
        this.eventCategory = eventCategory;
        this.title = title;
        this.description = description;
        this.date = date;
        this.location = location;
        this.image = image;
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

    public EventCategory getEventCategory() {
        return eventCategory;
    }

    public void setEventCategory(EventCategory eventCategory) {
        this.eventCategory = eventCategory;
    }

    public String getTitle() {
        return title;
    }

    public void setTitle(String title) {
        this.title = title;
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

    public String getLocation() {
        return location;
    }

    public void setLocation(String location) {
        this.location = location;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }


}