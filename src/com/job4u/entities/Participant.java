package com.job4u.entities;

public class Participant {

    private int id;
    private User user;
    private Event event;
    private String status;

    public Participant() {
    }

    public Participant(int id, User user, Event event, String status) {
        this.id = id;
        this.user = user;
        this.event = event;
        this.status = status;
    }

    public Participant(User user, Event event, String status) {
        this.user = user;
        this.event = event;
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

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }


}