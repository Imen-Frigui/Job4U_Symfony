package com.job4u.entities;

import java.util.Date;

public class Notification {

    private int id;
    private User user;
    private Event event;
    private String message;
    private int hasRead;
    private Date createdAt;
    private String status;

    public Notification() {
    }

    public Notification(int id, User user, Event event, String message, int hasRead, Date createdAt, String status) {
        this.id = id;
        this.user = user;
        this.event = event;
        this.message = message;
        this.hasRead = hasRead;
        this.createdAt = createdAt;
        this.status = status;
    }

    public Notification(User user, Event event, String message, int hasRead, Date createdAt, String status) {
        this.user = user;
        this.event = event;
        this.message = message;
        this.hasRead = hasRead;
        this.createdAt = createdAt;
        this.status = status;
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

    public Event getEvent() {
        return event;
    }

    public void setEvent(Event event) {
        this.event = event;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public int getHasRead() {
        return hasRead;
    }

    public void setHasRead(int hasRead) {
        this.hasRead = hasRead;
    }

    public Date getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(Date createdAt) {
        this.createdAt = createdAt;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }


}