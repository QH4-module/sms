DROP TABLE IF EXISTS `tbl_sms_code_ali`;

CREATE TABLE IF NOT EXISTS `tbl_sms_code_ali`
(
    `id`              INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `mobile`          VARCHAR(20)   NOT NULL COMMENT '手机号',
    `code`            VARCHAR(15)   NOT NULL COMMENT '短信验证码',
    `create_time`     BIGINT        NOT NULL COMMENT '创建时间',
    `expiration_time` BIGINT        NOT NULL COMMENT '失效时间',
    `is_used`         TINYINT       NOT NULL COMMENT '是否已经使用过',
    `response`        VARCHAR(1000) NULL COMMENT '阿里云返回的结果',
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    COMMENT = '短信验证码,阿里云发送';

CREATE INDEX `mobile_index` ON `tbl_sms_code_ali` (`mobile` ASC);
