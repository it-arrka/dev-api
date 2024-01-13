# Token Table

CREATE TABLE jwt_token (
    email text,
    status text,
    token_type text,
    jwt_token text,
    createdate timestamp,
    effectivedate timestamp,
    modifydate timestamp,
    PRIMARY KEY ((email, status, token_type), jwt_token)
) WITH CLUSTERING ORDER BY (jwt_token ASC);

CREATE TABLE user_active_session (
    email text,
    status text,
    access_token text,
    createdate timestamp,
    effectivedate timestamp,
    modifydate timestamp,
    user_agent text,
    companycode text,
    role text,
    law text,
    PRIMARY KEY ((email, status), access_token)
) WITH CLUSTERING ORDER BY (access_token ASC);
