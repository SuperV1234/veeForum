#########################################################################################
# TABLE
# * This table deals with thread notifications.
#########################################################################################
# HIERARCHY
# * Derives from: tbl_notification_base
#########################################################################################
create table tbl_notification_thread
(
	# Primary key
	id int auto_increment primary key,

	# Base
	id_base int not null,

	# Subscription
	id_subscription_thread int not null,

	# Newly created post
	id_post int not null,

	foreign key (id_base)
		references tbl_notification_base(id)
		on update cascade
		on delete cascade,

	foreign key (id_subscription_thread)
		references tbl_subscription_thread(id)
		on update cascade
		on delete no action, # Triggers do not get fired with 'cascade'

	foreign key (id_post)
		references tbl_content_post(id)
		on update cascade
		on delete no action # Triggers do not get fired with 'cascade'
)$
#########################################################################################