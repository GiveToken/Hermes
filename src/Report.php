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
     * @return array - an array of numbers
     */
    public function organizationGrowth()
    {
        return $this->execute_query("SELECT yr, wk,
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
            GROUP BY yr, wk"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets response rate numbers over time
     *
     * @return array - an array of numbers
     */
    public function responseRate()
    {
        return $this->execute_query("SELECT views.yr, views.wk,
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
            ORDER by views.yr, views.wk"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Gets usage growth numbers
     *
     * @return array - an array of numbers
     */
    public function usageGrowth()
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
     * @return array - an array of numbers
     */
    public function userGrowth()
    {
        return $this->execute_query(""
        )->fetch_all(MYSQLI_ASSOC);
    }
}
