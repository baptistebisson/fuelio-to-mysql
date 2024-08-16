create table if not exists consumption
(
    date         timestamp    not null
        primary key,
    odo          int          null,
    fuel         float        null,
    price        float        null,
    volume_price float        null,
    notes        text         null,
    average      float        null,
    city         varchar(255) null
);

create table if not exists cost_category
(
    id   int auto_increment
        primary key,
    name varchar(25) null
);

create table if not exists cost
(
    date          timestamp    not null
        primary key,
    title         varchar(255) null,
    odo           int          null,
    notes         text         null,
    cost          float        not null,
    cost_category int          not null,
    constraint cost_cost_category_id_fk
        foreign key (cost_category) references cost_category (id)
);

