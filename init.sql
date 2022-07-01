create table if not exists entity (
  id            char(16)        not null,
  type          varchar(16)     not null,
  author        varchar(255)    not null,
  tsCreate      datetime        not null default CURRENT_TIMESTAMP,
  tsUpdate      datetime        not null default CURRENT_TIMESTAMP,
  primary key(id)
) CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists video (
  id            char(16)        not null,
  title         tinytext        null,
  date          text            null,
  originalFile  tinytext     null,
  filesize      int             not null,
  duration      float           null,
  foreign key(id) references entity(id) on update cascade on delete cascade,
  primary key(id)
) CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists playlist (
  id            char(16)        not null,
  title         tinytext        not null,
  foreign key(id) references entity(id) on update cascade on delete cascade,
  primary key(id)
) CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists playlist_video (
  video_id      char(16)        not null,
  playlist_id   char(16)        not null,
  weight        int             not null        default 0,
  foreign key(video_id) references video(id) on update cascade on delete cascade,
  foreign key(playlist_id) references playlist(id) on update cascade on delete cascade,
  primary key(video_id, playlist_id)
) CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists entity_access (
  id            char(16)        not null,
  user          varchar(255)    not null,
  access_view   boolean         null,
  access_list   boolean         null,
  access_update boolean         null,
  access_delete boolean         null,
  primary key(id, user),
  foreign key(id) references entity(id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists entity_tag (
  id            char(16)        not null,
  tag           varchar(255)    not null,
  foreign key(id) references entity(id) on update cascade on delete cascade,
  primary key(id, tag)
) CHARACTER SET utf8 COLLATE utf8_bin;

create table if not exists process_queue (
  proc_id       integer         not null auto_increment,
  id            char(16)        not null,
  func          varchar(255)    not null,
  options       mediumtext      not null,
  result        mediumtext      null,
  submit        datetime        not null DEFAULT CURRENT_TIMESTAMP,
  start         datetime        null,
  end           datetime        null,
  primary key(proc_id),
  foreign key(id) references entity(id) on update cascade on delete cascade
) CHARACTER SET utf8 COLLATE utf8_bin;
