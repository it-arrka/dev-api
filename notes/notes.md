# Token Table

create table jwt_token(
    email text primary key,
    createdate timestamp,
    effectivedate timestamp,
    modifydatedate timestamp,
    access_token text,
    access_timestamp timestamp,
    access_expiry_seconds int,
    refresh_token text,
    refresh_timestamp timestamp,
    refresh_expiry_seconds int,
    secret_key text
);

//save all the data

create table user_active_session(
    email text primary key,
);
