CREATE TABLE "frk"."credit_regional_distribution" (
  "id" int4 NOT NULL DEFAULT NULL,
  "statistic_type" varchar(20) COLLATE "pg_catalog"."default" DEFAULT NULL,
  "city_code" varchar(20) COLLATE "pg_catalog"."default" DEFAULT NULL,
  "value" varchar(20) COLLATE "pg_catalog"."default" DEFAULT NULL
)
;
COMMENT ON COLUMN "frk"."credit_regional_distribution"."id" IS '主键ID';
COMMENT ON COLUMN "frk"."credit_regional_distribution"."statistic_type" IS '统计类型';
COMMENT ON COLUMN "frk"."credit_regional_distribution"."city_code" IS '城市编码';
COMMENT ON COLUMN "frk"."credit_regional_distribution"."value" IS '统计值';