<?php
namespace Sizzle;

/**
 * This class is for database reports.
 */
class Report extends \Sizzle\Bacon\DatabaseEntity
{
    /**
     * Gets organization growth numbers
     *
     * @param $type string - weekly (default) or monthly
     *
     * @return array - an array of numbers
     */
    public function organizationGrowth(string $type = 'weekly')
    {
        if ('monthly' == $type) {
            $query = "SELECT t4.*, t5.paying FROM
                (SELECT yr, mnth,
                DATE_FORMAT(created, '%Y %M') AS `Month`,
                COUNT(DISTINCT organization_id) as active_organizations
                FROM user,
                (
                (SELECT user_id, YEAR(created) as yr, MONTH(created) as mnth
                FROM web_request
                WHERE user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                GROUP BY YEAR(created), MONTH(created), user_id)
                UNION
                (SELECT recruiting_token.user_id,
                YEAR(web_request.created) as yr,
                MONTH(web_request.created) as mnth
                FROM web_request, recruiting_token
                WHERE web_request.user_id IS NULL
                AND recruiting_token.user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id,'%')
                GROUP BY YEAR(web_request.created), MONTH(web_request.created), recruiting_token.user_id)
                ) as t3
                WHERE user.id = t3.user_id
                GROUP BY yr, mnth) AS t4
                LEFT JOIN
                (SELECT SUM(IF(started <= STR_TO_DATE(CONCAT(yr,mnth,' Sunday'), '%X%V %W')
                  AND (ended IS NULL OR ended > STR_TO_DATE(CONCAT(yr,mnth,' Saturday'), '%X%V %W'))
                  ,1,0)) AS paying, yr, mnth
                FROM paying_organization,
                (SELECT YEAR(web_request.created) as yr,
                MONTH(web_request.created) as mnth
                FROM
                web_request
                GROUP BY YEAR(web_request.created), MONTH(web_request.created)) web_request
                GROUP BY yr, mnth
                ) AS t5
                ON t4.yr = t5.yr
                AND t4.mnth = t5.mnth";
        } else {
            $query = "SELECT t4.*, t5.paying FROM
                (SELECT yr, wk,
                STR_TO_DATE(CONCAT(yr,wk,' Sunday'), '%X%V %W') as `Week Starting`,
                COUNT(DISTINCT organization_id) as active_organizations
                FROM user,
                (
                (SELECT user_id, YEAR(created) as yr, WEEK(created) as wk
                FROM web_request
                WHERE user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                GROUP BY YEAR(created), WEEK(created), user_id)
                UNION
                (SELECT recruiting_token.user_id,
                YEAR(web_request.created) as yr,
                WEEK(web_request.created) as wk
                FROM web_request, recruiting_token
                WHERE web_request.user_id IS NULL
                AND recruiting_token.user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id,'%')
                GROUP BY YEAR(web_request.created), WEEK(web_request.created), recruiting_token.user_id)
                ) as t3
                WHERE user.id = t3.user_id
                GROUP BY yr, wk) AS t4
                LEFT JOIN
                (SELECT SUM(IF(started <= STR_TO_DATE(CONCAT(yr,wk,' Sunday'), '%X%V %W')
                  AND (ended IS NULL OR ended > STR_TO_DATE(CONCAT(yr,wk,' Saturday'), '%X%V %W'))
                  ,1,0)) AS paying, yr, wk
                FROM paying_organization,
                (SELECT YEAR(web_request.created) as yr,
                WEEK(web_request.created) as wk
                FROM
                web_request
                GROUP BY YEAR(web_request.created), WEEK(web_request.created)) web_request
                GROUP BY yr, wk
                ) AS t5
                ON t4.yr = t5.yr
                AND t4.wk = t5.wk";
        }
        return $this->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets response rate numbers over time
     *
     * @param $type string - weekly (default) or monthly
     *
     * @return array - an array of numbers
     */
    public function responseRate(string $type = 'weekly')
    {
        if ('monthly' == $type) {
            $query = "SELECT views.yr, views.mnth,
                `Month`,
                COALESCE(`Nonuser Token Views`, 0) as `Nonuser Token Views`,
                100*COALESCE(`Yeses`, 0)/COALESCE(`Nonuser Token Views`, 0) as `Yes %`,
                100*COALESCE(`Maybes`, 0)/COALESCE(`Nonuser Token Views`, 0) as `Maybe %`,
                100*COALESCE(`Nos`, 0)/COALESCE(`Nonuser Token Views`, 0) as `No %`,
                100*(COALESCE(`Yeses`, 0)+COALESCE(`Maybes`, 0)+COALESCE(`Nos`, 0))/COALESCE(`Nonuser Token Views`, 0) as `Overall %`
                FROM
                (SELECT COUNT(*) as `Nonuser Token Views`,
                YEAR(web_request.created) as yr,
                MONTH(web_request.created) as mnth,
                DATE_FORMAT(web_request.created, '%Y %M') AS `Month`
                FROM web_request
                WHERE web_request.user_id IS NULL
                AND web_request.uri LIKE '/token/recruiting/%'
                AND remote_ip NOT IN (SELECT remote_ip FROM web_request WHERE user_id IS NOT NULL)
                AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
                AND web_request.created > '2016-01-01'
                GROUP BY YEAR(web_request.created), MONTH(web_request.created)) as views
                LEFT JOIN
                (SELECT SUM(IF(response = 'Yes',1,0)) as `Yeses`,
                SUM(IF(response = 'Maybe',1,0)) as `Maybes`,
                SUM(IF(response = 'No',1,0)) as `Nos`,
                YEAR(recruiting_token_response.created) as yr,
                MONTH(recruiting_token_response.created) as mnth
                FROM recruiting_token_response
                WHERE email NOT IN (SELECT email_address FROM user)
                AND email NOT LIKE '%givetoken.com'
                AND email NOT IN ('test@test.com', 'test@gmail.com')
                GROUP BY YEAR(recruiting_token_response.created), MONTH(recruiting_token_response.created)) as reponses
                ON views.yr = reponses.yr AND views.mnth = reponses.mnth
                ORDER by views.yr, views.mnth";
        } else {
            $query = "SELECT views.yr, views.wk,
                STR_TO_DATE(CONCAT(views.yr,views.wk,' Sunday'), '%X%V %W') as `Week Starting`,
                COALESCE(`Nonuser Token Views`, 0) as `Nonuser Token Views`,
                100*COALESCE(`Yeses`, 0)/COALESCE(`Nonuser Token Views`, 0) as `Yes %`,
                100*COALESCE(`Maybes`, 0)/COALESCE(`Nonuser Token Views`, 0) as `Maybe %`,
                100*COALESCE(`Nos`, 0)/COALESCE(`Nonuser Token Views`, 0) as `No %`,
                100*(COALESCE(`Yeses`, 0)+COALESCE(`Maybes`, 0)+COALESCE(`Nos`, 0))/COALESCE(`Nonuser Token Views`, 0) as `Overall %`
                FROM
                (SELECT COUNT(*) as `Nonuser Token Views`,
                YEAR(web_request.created) as yr,
                WEEK(web_request.created) as wk
                FROM web_request
                WHERE web_request.user_id IS NULL
                AND web_request.uri LIKE '/token/recruiting/%'
                AND remote_ip NOT IN (SELECT remote_ip FROM web_request WHERE user_id IS NOT NULL)
                AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
                GROUP BY YEAR(web_request.created), WEEK(web_request.created)) as views
                LEFT JOIN
                (SELECT SUM(IF(response = 'Yes',1,0)) as `Yeses`,
                SUM(IF(response = 'Maybe',1,0)) as `Maybes`,
                SUM(IF(response = 'No',1,0)) as `Nos`,
                YEAR(recruiting_token_response.created) as yr,
                WEEK(recruiting_token_response.created) as wk
                FROM recruiting_token_response
                WHERE email NOT IN (SELECT email_address FROM user)
                AND email NOT LIKE '%givetoken.com'
                AND email NOT IN ('test@test.com', 'test@gmail.com')
                GROUP BY YEAR(recruiting_token_response.created), WEEK(recruiting_token_response.created)) as reponses
                ON views.yr = reponses.yr AND views.wk = reponses.wk
                ORDER by views.yr, views.wk";
        }
        return $this->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets usage growth numbers
     *
     * @param $type string - weekly (default) or monthly
     *
     * @return array - an array of numbers
     */
    public function usageGrowth(string $type = 'weekly')
    {
        return $this->execute_query("SELECT views.yr, views.wk,
            STR_TO_DATE(CONCAT(views.yr,views.wk,' Sunday'), '%X%V %W') as `Week Starting`,
            COALESCE(`Nonuser Token Views`, 0) as `Nonuser Token Views`,
            COALESCE(`Emails Sent`, 0) as `Emails Sent`
            FROM
            (SELECT COUNT(*) as `Nonuser Token Views`,
            YEAR(web_request.created) as yr,
            WEEK(web_request.created) as wk
            FROM web_request
            WHERE web_request.user_id IS NULL
            AND web_request.uri LIKE '/token/recruiting/%'
            AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
            GROUP BY YEAR(web_request.created), WEEK(web_request.created)) as views
            LEFT JOIN
            (SELECT COUNT(*) as `Emails Sent`,
            YEAR(email_sent.created) as yr,
            WEEK(email_sent.created) as wk
            FROM email_sent, email_credential, user
            WHERE email_sent.email_credential_id = email_credential.id
            AND email_credential.user_id = user.id
            AND email_sent.success = 'Yes'
            AND user.internal = 'N'
            GROUP BY YEAR(email_sent.created), WEEK(email_sent.created)) as emails
            ON views.yr = emails.yr AND views.wk = emails.wk
            ORDER by views.yr, views.wk"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets user growth numbers
     *
     * @param $type string - weekly (default) or monthly
     *
     * @return array - an array of numbers
     */
    public function userGrowth(string $type = 'weekly')
    {
        if ('monthly' == $type) {
            $query = "SELECT COUNT(DISTINCT user_id) as users, yr, mnth, `Month`
                FROM
                (
                (SELECT user_id, YEAR(created) as yr, MONTH(created) as mnth,
                DATE_FORMAT(created, '%Y %M') AS `Month`
                FROM web_request
                WHERE user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                GROUP BY YEAR(created), MONTH(created), user_id)
                UNION
                (SELECT recruiting_token.user_id,
                YEAR(web_request.created) as yr,
                MONTH(web_request.created) as mnth,
                DATE_FORMAT(web_request.created, '%Y %M') AS `Month`
                FROM web_request, recruiting_token
                WHERE web_request.user_id IS NULL
                AND recruiting_token.user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id,'%')
                GROUP BY YEAR(web_request.created), MONTH(web_request.created), recruiting_token.user_id)
                ) as t3
                GROUP BY yr, mnth";
        } else {
            $query = "SELECT COUNT(DISTINCT user_id) as users, yr, wk,
                STR_TO_DATE(CONCAT(yr, wk,' Sunday'), '%X%V %W') as `Week Starting`
                FROM
                (
                (SELECT user_id, YEAR(created) as yr, WEEK(created) as wk
                FROM web_request
                WHERE user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                GROUP BY YEAR(created), WEEK(created), user_id)
                UNION
                (SELECT recruiting_token.user_id,
                YEAR(web_request.created) as yr,
                WEEK(web_request.created) as wk
                FROM web_request, recruiting_token
                WHERE web_request.user_id IS NULL
                AND recruiting_token.user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id,'%')
                GROUP BY YEAR(web_request.created), WEEK(web_request.created), recruiting_token.user_id)
                ) as t3
                GROUP BY yr, wk";
        }
        return $this->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets info on inactive organizations
     *
     * @return array - an array of numbers
     */
    public function inactiveOrganizations()
    {
        return $this->execute_query("SELECT
            weekly.id, weekly.`name`, weekly.created, weekly.paying_user,
            COALESCE(monthly.inactive, weekly.inactive) AS inactive
            FROM
            (SELECT distinct organization.id, organization.`name`, organization.created, organization.paying_user, 'Week' AS inactive
                FROM organization
                WHERE organization.id NOT IN
                (SELECT organization_id
                    FROM user
                    WHERE user.id IN (
                        SELECT distinct user_id
                        FROM web_request
                        WHERE user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                        AND web_request.created > DATE_SUB(NOW(), INTERVAL 1 WEEK)
                        AND user_id is not null
                        UNION
                        SELECT distinct recruiting_token.user_id
                        FROM web_request, recruiting_token
                        WHERE web_request.user_id IS NULL
                        AND web_request.created > DATE_SUB(NOW(), INTERVAL 1 WEEK)
                        AND recruiting_token.user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                        AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id,'%')
                    )
                )
                AND (organization.paying_user IS NOT NULL
                    OR organization.created > DATE_SUB(NOW(), INTERVAL 1 MONTH))
                AND organization.id != 1
            ) as weekly
            LEFT JOIN (SELECT distinct organization.id, organization.`name`, organization.created, organization.paying_user, 'Month' AS inactive
                FROM organization
                WHERE organization.id NOT IN
                (SELECT organization_id
                    FROM user
                    WHERE user.id IN (
                        SELECT distinct user_id
                        FROM web_request
                        WHERE user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                        AND web_request.created > DATE_SUB(NOW(), INTERVAL 1 MONTH)
                        AND user_id is not null
                        UNION
                        SELECT distinct recruiting_token.user_id
                        FROM web_request, recruiting_token
                        WHERE web_request.user_id IS NULL
                        AND web_request.created > DATE_SUB(NOW(), INTERVAL 1 MONTH)
                        AND recruiting_token.user_id NOT IN (SELECT id from user WHERE internal = 'Y')
                        AND web_request.uri LIKE CONCAT('/token/recruiting/', recruiting_token.long_id,'%')
                    )
                )
                AND (organization.paying_user IS NOT NULL
                    OR organization.created > DATE_SUB(NOW(), INTERVAL 1 MONTH))
                AND organization.id != 1
            ) AS monthly on weekly.id = monthly.id
            ORDER BY inactive"
        );//->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets token source numbers
     *
     * @return array - an array of numbers
     */
    public function tokenSource()
    {
        return $this->execute_query("SELECT
            STR_TO_DATE(CONCAT(YEAR(created), WEEK(created),' Sunday'), '%X%V %W') as `Week Starting`,
            SUM(IF(uri like '/token/recruiting%?source=twitter%',1,0)) Twitter,
            SUM(IF(uri like '/token/recruiting%?source=facebook%',1,0)) Facebook,
            SUM(IF(uri like '/token/recruiting%?source=linkedin%',1,0)) LinkedIn,
            SUM(IF(uri not like '%?source=%',1,0)) Other,
            COUNT(*) total
            FROM web_request
            WHERE uri LIKE '/token/recruiting%'
            AND web_request.user_id IS NULL
            AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
            GROUP BY `Week Starting`
            ORDER BY `Week Starting`;"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets token os numbers
     *
     * @return array - an array of numbers
     */
    public function tokenOS()
    {
        return $this->execute_query("SELECT
            STR_TO_DATE(CONCAT(YEAR(created), WEEK(created),' Sunday'), '%X%V %W') as `Week Starting`,
            SUM(IF(user_agent LIKE '%Macintosh%',1, 0)) as osx,
            SUM(IF(user_agent LIKE '%iPad%',1, 0)) as ipad,
            SUM(IF(user_agent LIKE '%iPhone OS%',1, 0)) as iphone,
            SUM(IF(user_agent LIKE '%Android%',1, 0)) as android,
            SUM(IF(user_agent LIKE '%Windows%',1, 0)) as windows,
            COUNT(*) total
            FROM giftbox.web_request
            WHERE uri LIKE '/token/recruiting%'
            AND web_request.user_id IS NULL
            AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
            GROUP BY `Week Starting`
            ORDER BY `Week Starting`"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets token browser numbers
     *
     * @return array - an array of numbers
     */
    public function tokenBrowser()
    {
        return $this->execute_query("SELECT
            STR_TO_DATE(CONCAT(YEAR(created), WEEK(created),' Sunday'), '%X%V %W') as `Week Starting`,
            SUM(IF(user_agent LIKE '%Chrome%' AND user_agent NOT LIKE '%Edge%',1, 0)) as chrome,
            SUM(IF(user_agent LIKE '%Firefox%',1, 0)) as firefox,
            SUM(IF(user_agent LIKE '%Trident%',1, 0)) as ie,
            SUM(IF(user_agent LIKE '%AppleWebKit%' AND user_agent NOT LIKE '%Chrome%' ,1, 0)) as safari,
            SUM(IF(user_agent LIKE '%Edge%',1, 0)) as edge,
            COUNT(*) total
            FROM giftbox.web_request
            WHERE uri LIKE '/token/recruiting%'
            AND user_id IS NULL
            AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
            GROUP BY `Week Starting`
            ORDER BY `Week Starting`"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets token org numbers
     *
     * @return array - an array of numbers
     */
    public function tokenOrganization()
    {
        $raw = $this->execute_query("SELECT
            STR_TO_DATE(CONCAT(YEAR(t0.created), WEEK(t0.created),' Sunday'), '%X%V %W') as `Week Starting`,
            org_name,
            COUNT(*) AS cnt
            FROM
            (SELECT * FROM web_request
            WHERE uri LIKE '/token/recruiting%'
            AND web_request.user_id IS NULL
            AND user_agent NOT IN (SELECT user_agent FROM bot_user_agent WHERE deleted IS NULL)
            ) AS t0
            JOIN
            (SELECT recruiting_token.long_id, organization.`name` AS org_name
            FROM recruiting_token, user, organization
            WHERE recruiting_token.user_id = user.id
            AND user.organization_id = organization.id
            ) AS t1 on t0.`uri` LIKE CONCAT('%',t1.long_id,'%')
            GROUP BY t1.org_name, `Week Starting`
            ORDER BY `Week Starting`, cnt DESC;"
        )->fetch_all(MYSQLI_ASSOC);
        $return = array();
        foreach ($raw as $row) {
            $return[$row['Week Starting']]['Week Starting'] = $row['Week Starting'];
            $return[$row['Week Starting']][$row['org_name']] = $row['cnt'];
            $return[$row['Week Starting']]['total'] = $row['cnt'] + ($return[$row['Week Starting']]['total'] ?? 0);
        }
        return $return;
    }

    /**
     * Gets a random color
     *
     * @return string - random rgb value
     */
    public function randRGBA()
    {
        return "rgba(".rand(1,255).','.rand(1,255).','.rand(1,255).','.rand(1,255).")";
    }

    /**
     * Gets tokens created numbers
     *
     * @param $type string - weekly (default) or monthly
     *
     * @return array - an array of numbers
     */
    public function tokensCreated(string $type = 'weekly')
    {
        if ('monthly' == $type) {
            $query = "SELECT
                DATE_FORMAT(created, '%Y %M') AS `Month`,
                COUNT(*) tokens
                FROM recruiting_token
                WHERE user_id NOT IN (SELECT id FROM user WHERE organization_id = 1)
                GROUP BY YEAR(created), MONTH(created)
                ORDER BY YEAR(created), MONTH(created)";
        } else {
            $query = "SELECT
                STR_TO_DATE(CONCAT(YEAR(created), WEEK(created),' Sunday'), '%X%V %W') as `Week Starting`,
                COUNT(*) tokens
                FROM recruiting_token
                WHERE user_id NOT IN (SELECT id FROM user WHERE organization_id = 1)
                GROUP BY `Week Starting`
                ORDER BY `Week Starting`";
        }
        return $this->execute_query($query)->fetch_all(MYSQLI_ASSOC);
    }
}
