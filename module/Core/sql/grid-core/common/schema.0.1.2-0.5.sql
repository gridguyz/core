--------------------------------------------------------------------------------
-- function: string_set_append( text, text, text, text )                      --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."string_set_append"(
    "p_list"        TEXT,
    "p_separator"   TEXT,
    "p_null"        TEXT,
    "p_append"      TEXT
)
     RETURNS TEXT
             IMMUTABLE
             CALLED ON NULL INPUT
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_set"     TEXT ARRAY;
BEGIN

    IF "p_append" IS NULL THEN
        RETURN "p_list";
    END IF;

    IF "p_list" IS NULL THEN
        RETURN "p_append";
    ELSE
        "v_set" = string_to_array( "p_list", "p_separator", "p_null" );
    END IF;

    IF "p_append" = ANY ( "v_set" ) THEN
        RETURN "p_list";
    ELSE
        "v_set" = array_append( "v_set", "p_append" );
    END IF;

    RETURN array_to_string( "v_set", "p_separator", "p_null" );

END $$;

--------------------------------------------------------------------------------
-- function: string_set_append( text, text, text )                            --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."string_set_append"(
    "p_list"        TEXT,
    "p_separator"   TEXT,
    "p_append"      TEXT
)
     RETURNS TEXT
             IMMUTABLE
             CALLED ON NULL INPUT
    LANGUAGE SQL
          AS $$
      SELECT "_common"."string_set_append"(
                 "p_list",
                 "p_separator",
                 NULL,
                 "p_append"
             ) $$;

--------------------------------------------------------------------------------
-- function: string_set_remove( text, text, text, text )                      --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."string_set_remove"(
    "p_list"        TEXT,
    "p_separator"   TEXT,
    "p_null"        TEXT,
    "p_remove"      TEXT
)
     RETURNS TEXT
             IMMUTABLE
             CALLED ON NULL INPUT
    LANGUAGE plpgsql
          AS $$
DECLARE
    "v_set"     TEXT ARRAY;
BEGIN

    IF "p_remove" IS NULL THEN
        RETURN "p_list";
    END IF;

    IF "p_list" IS NULL THEN
        RETURN NULL;
    ELSE
        "v_set" = string_to_array( "p_list", "p_separator", "p_null" );
    END IF;

    IF "p_remove" = ANY ( "v_set" ) THEN
        "v_set" = (
            SELECT array_agg( "element" )
              FROM unnest( "v_set" ) AS "set" ( "element" )
             WHERE "element" <> "p_remove"
        );
    ELSE
        RETURN "p_list";
    END IF;

    RETURN array_to_string( "v_set", "p_separator", "p_null" );

END $$;

--------------------------------------------------------------------------------
-- function: string_set_remove( text, text, text )                            --
--------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION "_common"."string_set_remove"(
    "p_list"        TEXT,
    "p_separator"   TEXT,
    "p_remove"      TEXT
)
     RETURNS TEXT
             IMMUTABLE
             CALLED ON NULL INPUT
    LANGUAGE SQL
          AS $$
      SELECT "_common"."string_set_remove"(
                 "p_list",
                 "p_separator",
                 NULL,
                 "p_remove"
             ) $$;
