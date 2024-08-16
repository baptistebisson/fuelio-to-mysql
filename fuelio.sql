create table if not exists cost_category
(
    id   int auto_increment
        primary key,
    name varchar(25) null
);

create table if not exists vehicle
(
    id          int          not null
        primary key,
    name        varchar(20)  null,
    description varchar(255) null
);

create table if not exists consumption
(
    vehicle_id   int          not null,
    date         timestamp    not null
        primary key,
    odo          int          null,
    fuel         float        null,
    price        float        null,
    volume_price float        null,
    notes        text         null,
    average      float        null,
    city         varchar(255) null,
    constraint consumption_vehicle_id_fk
        foreign key (vehicle_id) references vehicle (id)
);

create table if not exists cost
(
    vehicle_id    int          not null,
    date          timestamp    not null
        primary key,
    title         varchar(255) null,
    odo           int          null,
    notes         text         null,
    cost          float        not null,
    cost_category int          not null,
    constraint cost_cost_category_id_fk
        foreign key (cost_category) references cost_category (id),
    constraint cost_vehicle_id_fk
        foreign key (vehicle_id) references vehicle (id)
);

