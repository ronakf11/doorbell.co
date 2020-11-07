create database psddemo;
use psddemo;

CREATE TABLE HOODS (
    hood_id int AUTO_INCREMENT,
    hood_name varchar(255),
    PRIMARY KEY (hood_id)
);

CREATE TABLE BLOCKS (
    block_id int AUTO_INCREMENT,
    hood_id int,
    block_name varchar(255),
    city_id int,
    city_name varchar(255),
    block_latitude double NOT NULL,
    block_longitude double NOT NULL,
    PRIMARY KEY (block_id,city_id),
    FOREIGN KEY (hood_id) REFERENCES HOODS(hood_id)
);

CREATE TABLE REGISTER_TEMP (
    temp_cust_id INT NOT NULL AUTO_INCREMENT,
    cust_name varchar(255) NOT NULL,
    cust_email varchar(255) NOT NULL,
    city_id int NOT NULL,
    block_id int NOT NULL,
    apt_num int NOT NULL,
	cust_intro varchar(4000) not null,
	cust_photo blob,
    password varchar(255) NOT NULL,
	active_status varchar(255) NOT NULL,
	UNIQUE(cust_email,active_status),
    PRIMARY KEY (temp_cust_id),
    FOREIGN KEY (block_id,city_id) REFERENCES BLOCKS(block_id,city_id)
);

CREATE TABLE CUSTOMER (
    cust_id INT NOT NULL AUTO_INCREMENT,
    prev_cust_id int,
    cust_name varchar(255) NOT NULL,
    cust_email varchar(255) NOT NULL,
    city_id int NOT NULL,
    block_id int NOT NULL,
    apt_num int NOT NULL,
	cust_intro varchar(4000) not null,
    cust_photo blob,
    password varchar(255) NOT NULL,
    join_time datetime,
    active_status varchar(255),
    PRIMARY KEY (cust_id),
    FOREIGN KEY (block_id,city_id) REFERENCES BLOCKS(block_id,city_id),
    FOREIGN KEY (prev_cust_id) REFERENCES CUSTOMER(cust_id)
);

CREATE TABLE REQUEST_APPROVAL (
    temp_cust_id int,
    approver_id int,
    approval_request_date datetime,
    approval_date datetime,
    approval_status varchar(255),
    PRIMARY KEY (temp_cust_id,approver_id),
    FOREIGN KEY (temp_cust_id) REFERENCES REGISTER_TEMP(temp_cust_id),
    FOREIGN KEY (approver_id) REFERENCES CUSTOMER(cust_id)
);



CREATE TABLE FRIENDS (
    sent_by_cust int,
    received_by_cust int,
    request_status varchar(255),
    request_date datetime,
    response_date datetime,
    PRIMARY KEY (sent_by_cust, received_by_cust, request_date),
    FOREIGN KEY (sent_by_cust) REFERENCES CUSTOMER(cust_id),
    FOREIGN KEY (received_by_cust) REFERENCES CUSTOMER(cust_id)
);

CREATE TABLE DIRECT_NEIGHBOURS (
    user_id int,
    neighbour_id int,
    added_date datetime,
    PRIMARY KEY (user_id,neighbour_id,added_date),
    FOREIGN KEY (user_id) REFERENCES CUSTOMER(cust_id),
    FOREIGN KEY (neighbour_id) REFERENCES CUSTOMER(cust_id)
);

CREATE TABLE THREADS (
    thread_id int AUTO_INCREMENT,
    thread_subject varchar(255),
    initiated_by int,
    initiated_date datetime,
    recipient_type varchar(255),
    recipient_user_id int,
    PRIMARY KEY (thread_id),
    FOREIGN KEY (initiated_by) REFERENCES CUSTOMER(cust_id),
    FOREIGN KEY (recipient_user_id) REFERENCES CUSTOMER(cust_id)
);

CREATE TABLE MESSAGES (
    thread_id int,
    message_id int AUTO_INCREMENT,
    message_author_id int,
    message_body varchar(255),
    message_location varchar(255),
    message_date datetime,
    PRIMARY KEY (message_id),
    FOREIGN KEY (message_author_id) REFERENCES CUSTOMER(cust_id),
    FOREIGN KEY (thread_id) REFERENCES THREADS(thread_id)
);

CREATE TABLE MESSAGE_BROADCAST (
    message_id int,
    message_receiver_id int,
    message_read varchar(255),
    PRIMARY KEY (message_id,message_receiver_id),
    FOREIGN KEY (message_receiver_id) REFERENCES CUSTOMER(cust_id),
    FOREIGN KEY (message_id) REFERENCES MESSAGES(message_id)
);

CREATE TABLE LOGIN_HISTORY (
    cust_id int,
    login_time datetime,
    logout_time datetime,
    PRIMARY KEY (cust_id,login_time),
    FOREIGN KEY (cust_id) REFERENCES CUSTOMER(cust_id)
);
