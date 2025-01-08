package com.wki.payservices.model;

public class Service {

    String id, name, icon, charges;

    public Service(String id, String name, String icon) {
        this.id = id;
        this.name = name;
        this.icon = icon;
    }

    public Service(String id, String name, String icon, String charges) {
        this.id = id;
        this.name = name;
        this.icon = icon;
        this.charges = charges;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getIcon() {
        return icon;
    }

    public void setIcon(String icon) {
        this.icon = icon;
    }

    public String getCharges() {
        return charges;
    }

    public void setCharges(String charges) {
        this.charges = charges;
    }
}
