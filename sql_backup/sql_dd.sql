/* This is the SQL File for the eClub Project */

/* Users Table */
CREATE TABLE IF NOT EXISTS e_user  (
	`user_name`         varchar(30),
	`user_pass`         text,
	`user_id`           int NULL AUTO_INCREMENT,
	`user_dob`          DATE,
	`user_email`        text NOT NULL,
	PRIMARY KEY (`user_id`)
);

/* Items Table */
CREATE TABLE IF NOT EXISTS e_item	(
	`item_name`			varchar(30),
	`item_make_time`	int,
	`item_id`			int NULL AUTO_INCREMENT,
	PRIMARY KEY (`item_id`)
);

/* Item Requirement Table */
CREATE TABLE IF NOT EXISTS e_require	(
	`item_id` 			int,
	`req_item_id`		int,
	`req_item_qty`		int,
	PRIMARY KEY(`item_id`, `req_item_id`),
	FOREIGN KEY(`item_id`) REFERENCES `e_item`(`item_id`),
	FOREIGN KEY(`req_item_id`) REFERENCES `e_item`(`item_id`)
);

/* Inventory Table */
CREATE TABLE IF NOT EXISTS e_inventory	(
	user_id				int,
	item_id				int,
	item_qty			int,
	PRIMARY KEY (`user_id`, `item_id`),
	FOREIGN KEY(`user_id`) REFERENCES `e_user`(`user_id`),
	FOREIGN KEY(`item_id`) REFERENCES `e_item`(`item_id`)
);

/* Loan Table */
CREATE TABLE IF NOT EXISTS e_load	(
	user_id				int,
	load_amount			float,
	PRIMARY KEY(`user_id`),
	FOREIGN KEY (`user_id`) REFERENCES `e_user`(`user_id`)
);

/* Auctions Table */
CREATE TABLE IF NOT EXISTS e_auction	(
	user_id				int,
	item_id				int,
	current_bid			float,
	current_bidder		int,
	buyout_price		float,
	item_qty			int,
	FOREIGN KEY (`user_id`) REFERENCES `e_user`(`user_id`),
	FOREIGN KEY (`current_bidder`) REFERENCES `e_user`(`user_id`),
	FOREIGN KEY (`item_id`) REFERENCES `e_item`(`item_id`)
);

/* Production Table */
CREATE TABLE IF NOT EXISTS e_production	(
	user_id				int,
	item_id				int,
	time_left			int,
	FOREIGN KEY (`user_id`) REFERENCES `e_user`(`user_id`),
	FOREIGN KEY (`item_id`) REFERENCES `e_item`(`item_id`)
);