DELIMITER //

CREATE PROCEDURE tempRegistration
(IN cust_name varchar(255),IN cust_email varchar(255),IN city_id int,in block_id int, in apt_num int, in cust_intro varchar(4000), in cust_photo blob, in password varchar(255))

begin

INSERT INTO REGISTER_TEMP (cust_name, cust_email, city_id, block_id, apt_num, cust_intro,cust_photo, password,active_status)
VALUES (cust_name, cust_email, city_id, block_id, apt_num, cust_intro,cust_photo, password,'active');

END //
DELIMITER ;

---------------------------------------------------------

drop trigger if exists customer_approval_requests;

DELIMITER //

create trigger customer_approval_requests after insert
on REGISTER_TEMP for each row begin 

declare total_existing_users_block integer;
declare approver integer;
declare finished integer default 0;
declare curApprovers 
	cursor for
select cust_id from CUSTOMER where block_id = new.block_id
and active_status='active';
	-- declare NOT FOUND handler
declare continue HANDLER for not found set finished = 1;
select count(*) into total_existing_users_block
from CUSTOMER where block_id = new.block_id and active_status='active';

if total_existing_users_block = 0 then
	-- if no existing users then auto approve
	insert  into CUSTOMER
		(prev_cust_id, cust_name, cust_email, city_id, block_id, apt_num, cust_intro, cust_photo, password, join_time, active_status)
		values
		(null
		  , new.cust_name, new.cust_email, new.city_id, new.block_id, new.apt_num, new.cust_intro, new.cust_photo, new.password, current_timestamp, 'active');

else
	open curApprovers;
	getApprover:loop
		fetch curApprovers into approver;
		
		if finished = 1 then
			leave getApprover;
		end if;
	
		insert  into REQUEST_APPROVAL
			(temp_cust_id, approver_id, approval_request_date, approval_date, approval_status
			)
			values
			(new.temp_cust_id, approver, current_timestamp, null,'pending');
	
	end loop getApprover;
	close curApprovers;
end if;
end; //

DELIMITER ;



drop trigger if exists requests_approved;

DELIMITER //

create trigger requests_approved after update
on REQUEST_APPROVAL for each row begin 
	
declare total_approval_requests integer;	
declare approved_requests integer;
declare is_address_update integer;
declare is_already_approved integer;
declare v_prev_cust_id integer;
select count(*) into total_approval_requests
from REQUEST_APPROVAL
where temp_cust_id = old.temp_cust_id;

select count(*) into approved_requests
from REQUEST_APPROVAL
where temp_cust_id = old.temp_cust_id
and approval_status='approved';

-- To check if the customer is making an address update
select count(*) into is_address_update from customer c where c.cust_email =(
select distinct cust_email from register_temp rt 
where rt.temp_cust_id=old.temp_cust_id)
and active_status='deactive';

-- To check if the customer is already approved
select count(*) into is_already_approved from customer c where c.cust_email =(
select distinct cust_email from register_temp rt 
where rt.temp_cust_id=old.temp_cust_id)
and active_status='active';

if(is_already_approved = 0) then
	if total_approval_requests <3 then
		if(total_approval_requests=approved_requests) then
			insert  into CUSTOMER
			(prev_cust_id, cust_name, cust_email, city_id, block_id, apt_num, cust_intro, cust_photo, password, join_time, active_status)
			select null as prev_cust_id, cust_name, cust_email, city_id, block_id, apt_num, cust_intro,cust_photo, password, current_timestamp as join_time, 'active' as active_status
			from register_temp
			where temp_cust_id=old.temp_cust_id;
		
			if is_address_update >0 then
				select cust_id into v_prev_cust_id from customer c 
				where c.cust_email =(select distinct cust_email from register_temp rt 
							 where rt.temp_cust_id=old.temp_cust_id)
				and active_status='deactive' order by join_time desc limit 1; 
				
				update customer set prev_cust_id=v_prev_cust_id 
				where cust_email =(select distinct cust_email from register_temp rt 
							 where rt.temp_cust_id=old.temp_cust_id)
				and active_status='active';
			end if;
		end if;
else
		if(approved_requests >= 3) then
			insert  into CUSTOMER
			(prev_cust_id, cust_name, cust_email, city_id, block_id, apt_num, cust_intro, cust_photo, password, join_time, active_status)
			select null as prev_cust_id, cust_name, cust_email, city_id, block_id, apt_num, cust_intro,cust_photo, password, current_timestamp as join_time, 'active' as active_status
			from register_temp
			where temp_cust_id=old.temp_cust_id;
		
			if is_address_update >0 then
				select cust_id into v_prev_cust_id from customer c 
				where c.cust_email =(select distinct cust_email from register_temp rt 
						where rt.temp_cust_id=old.temp_cust_id)
		and active_status='deactive' order by join_time desc limit 1; 
				
				update customer set prev_cust_id=v_prev_cust_id 
				where cust_email =(select distinct cust_email from register_temp rt 
							 where rt.temp_cust_id=old.temp_cust_id)
				and active_status='active';
			
			end if;
		end if;
	end if;
end if;

end; //
DELIMITER ;
---------------------------------------------------------

DELIMITER //

CREATE PROCEDURE updateAddress
(IN v_cust_id int,IN v_city_id int,IN v_block_id int,in v_apt_num int)
begin

DECLARE v_cust_email varchar(255);
DECLARE v_cust_name varchar(255);
DECLARE v_cust_intro varchar(255);
DECLARE v_cust_photo blob;
DECLARE v_password varchar(255);

select cust_name, cust_email, cust_intro,cust_photo, password
into v_cust_name, v_cust_email, v_cust_intro,v_cust_photo, v_password 
from customer 
where cust_id=v_cust_id
and active_status='active';

update customer 
set active_status='deactive'
where cust_id=v_cust_id
and active_status='active';

update register_temp 
set active_status='deactive'
where cust_email=v_cust_email
and active_status='active';

INSERT INTO REGISTER_TEMP (cust_name, cust_email, city_id, block_id, apt_num, cust_intro,cust_photo, password,active_status)
VALUES (v_cust_name, v_cust_email, v_city_id, v_block_id, v_apt_num, v_cust_intro,v_cust_photo, v_password,'active');
END
---------------------------------------------------------

DELIMITER //

CREATE PROCEDURE createThread
(IN thread_subject varchar(4000), IN thread_content varchar(4000), IN initiated_by int, IN recipient_type VARCHAR(30), IN recipient_user_id int,in gps_coordinates double)

begin
DECLARE v_thread_id INT;
set @currentTime = now();

INSERT INTO THREADS (thread_subject, initiated_by, initiated_date, recipient_type, recipient_user_id)
VALUES 
(thread_subject, initiated_by ,@currentTime, recipient_type, recipient_user_id);

select max(thread_id) into v_thread_id from THREADS;

INSERT INTO MESSAGES (thread_id, message_author_id, message_body, message_location, message_date)
VALUES 
(v_thread_id, initiated_by, thread_content , gps_coordinates , @currentTime);

END //

DELIMITER ;

---------------------------------------------------------

drop trigger if exists message_broadcasts;

DELIMITER //

create trigger message_broadcasts after insert
on MESSAGES  for each row begin 
	
declare v_recipient_type varchar(255);
declare v_recipient_user_id integer;
declare v_initiated_by integer;

declare finished integer default 0;

declare curallFriends 
	cursor for
		select received_by_cust as recipients from FRIENDS 
		where sent_by_cust = (select initiated_by from threads where thread_id = new.Thread_id)
		and request_status='approved'
		union
		select sent_by_cust as recipients from FRIENDS 
		where received_by_cust = (select initiated_by from threads where thread_id = new.Thread_id)
		and request_status='approved';
	

declare curblock 
	cursor for
			select cust_id as recipients  from customer 
		where block_id=(	
			select block_id from customer 
			where cust_id=(select initiated_by from threads where thread_id = new.Thread_id)
			and active_status='active')
		and active_status='active'
		and cust_id <> (select initiated_by from threads where thread_id = new.Thread_id);
	

declare curhood 
	cursor for
		select a.cust_id as recipients from customer a, blocks b
		where a.block_id=b.block_id
		and b.hood_id = (select hood_id from customer, blocks 
			where cust_id=(select initiated_by from threads where thread_id = new.Thread_id)
			and customer.block_id=blocks.block_id)
		and a.active_status='active'
		and a.cust_id <> (select initiated_by from threads where thread_id = new.Thread_id);
	
	-- declare NOT FOUND handler
declare continue HANDLER for not found set finished = 1;
select recipient_type,initiated_by into v_recipient_type,v_initiated_by from threads where thread_id =new.Thread_id;

if(v_initiated_by=new.message_author_id) then
INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
	VALUES (new.message_id, v_initiated_by, 'READ');
else
INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
	VALUES (new.message_id, v_initiated_by, 'UNREAD');
end if;

if(v_recipient_type= 'friend' or v_recipient_type= 'direct_neighbour') then
	select recipient_user_id into v_recipient_user_id from threads where thread_id =new.Thread_id;

	if(v_recipient_user_id=new.message_author_id) then
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'READ');
	else
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'UNREAD');
	end if;

elseif (v_recipient_type='allfriends') then
	open curallFriends;
	allfrnds:loop
		fetch curallFriends into v_recipient_user_id;
		
		if finished = 1 then
			leave allfrnds;
		end if;
	if(v_recipient_user_id=new.message_author_id) then
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'READ');
	else
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'UNREAD');
	end if;
	
	end loop allfrnds;
	close curallFriends;
elseif (v_recipient_type='block') then
	open curblock;
	allblock:loop
		fetch curblock into v_recipient_user_id;
		
		if finished = 1 then
			leave allblock;
		end if;
	if(v_recipient_user_id=new.message_author_id) then
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'READ');
	else
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'UNREAD');
	end if;
	
	end loop allblock;
	close curblock;

elseif (v_recipient_type='hood') then
	open curhood;
	allhood:loop
		fetch curhood into v_recipient_user_id;
		
		if finished = 1 then
			leave allhood;
		end if;
	if(v_recipient_user_id=new.message_author_id) then
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'READ');
	else
		INSERT INTO MESSAGE_BROADCAST (message_id, message_receiver_id, message_read)
		VALUES (new.message_id, v_recipient_user_id, 'UNREAD');
	end if;
	
	end loop allhood;
	close curhood;

end if;
end; //
DELIMITER ;

---------------------------------------------------------

DELIMITER //
CREATE PROCEDURE createNewMessage
(IN thread_id int,IN message_author_id int,IN message_body varchar(4000),in
gps_coordinates double)
begin
DECLARE v_thread_id INT;
set @currentTime = now();
INSERT INTO MESSAGES (thread_id, message_author_id, message_body,
message_location, message_date)
VALUES
(thread_id, message_author_id, message_body , gps_coordinates , @currentTime);
END //
DELIMITER ;

---------------------------------------------------------

DELIMITER //

CREATE PROCEDURE createNewMessage
(IN thread_id int,IN message_author_id int,IN message_body varchar(4000),in gps_coordinates double)

begin

DECLARE v_thread_id INT;

set @currentTime = now();


INSERT INTO MESSAGES (thread_id, message_author_id, message_body, message_location, message_date)
VALUES 
(thread_id, message_author_id, message_body , gps_coordinates , @currentTime);

END //

DELIMITER ;

---------------------------------------------------------

DELIMITER //

CREATE PROCEDURE addFriend
(IN sent_by int, sent_to int)
begin
	INSERT INTO FRIENDS (sent_by_cust, received_by_cust, request_status, request_date, response_date)
VALUES 
(sent_by, sent_to, 'pending', current_timestamp, null);

END //

DELIMITER ;

---------------------------------------------------------

DELIMITER //

CREATE PROCEDURE addneighbour
(IN added_by int, added_user int)
begin
	INSERT INTO DIRECT_NEIGHBOURS (user_id, neighbour_id, added_date)
VALUES 
(added_by, added_user, current_timestamp);

END //

DELIMITER ;
