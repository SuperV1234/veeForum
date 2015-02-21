#########################################################################################
# PROCEDURE
# * Generate notifications for every subscriber to the tag of the
#   last created content.
#########################################################################################
create procedure generate_notifications_tag()
begin
	declare loop_done int default false;
	declare var_id_sub, var_id_sub_base, var_id_sub_tracked_tag, 
			current_id_subscriptor int;
	declare itr cursor for select id, id_base, id_tag from tbl_subscription_tag;
	declare continue handler for not found set loop_done = true;

	open itr;

	# Get useful variables
	select id_tag, id_content
	into @last_tc_tag, @last_tc_content
	from tbl_tag_content
	order by id desc limit 1;

	label_loop:
	loop
		fetch itr into var_id_sub, var_id_sub_base, var_id_sub_tracked_tag;

		if loop_done then
			leave label_loop;
		end if;

		if var_id_sub_tracked_tag = @last_tc_tag then
			call get_subscriptor(var_id_sub_base, current_id_subscriptor);
			call mk_notification_tag(current_id_subscriptor, var_id_sub);
		end if;
	end loop;

	close itr;
end$
#########################################################################################