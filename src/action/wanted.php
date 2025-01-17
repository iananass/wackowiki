<?php

if (!defined('IN_WACKO'))
{
	exit;
}

$info = <<<EOD
Description:
	Shows pages that do not yet exist and are linked to for some cluster.

	By default it is equal to the current page. To display it for all namespaces, use root page="/".

Usage:
	{{wanted}}

Options:
	[page="Cluster"]
	[max=Number]
EOD;

$load_wanted = function ($cluster, $limit)
{
	$pagination	= [];
	$pref		= $this->prefix;

	$selector =
		'SELECT DISTINCT l.to_tag AS wanted_tag ' .
			'FROM ' . $pref . 'page_link l ' .
				'LEFT JOIN ' . $pref . 'page p ON ' .
					'((l.to_tag = p.tag ' .
						'AND l.to_page_id <> 0)) ' .
			'WHERE ' .
				($cluster
					? 'l.to_tag LIKE ' . $this->db->q($cluster . '/%') . ' AND '
					: '') .
				'p.tag is NULL GROUP BY wanted_tag ';

	// count pages
	if ($count = $this->db->load_single(
		'SELECT COUNT(*) AS n
		FROM ( ' .
			$selector .
		') AS src'
		, true))
	{
		$pagination = $this->pagination($count['n'], $limit);

		$wanted = $this->db->load_all(
				$selector .
				'ORDER BY wanted_tag ASC ' .
				$pagination['limit']);

		return [$wanted, $pagination];
	}
};

// set defaults
$help	??= 0;
$page	??= '';
$max	??= null;

if ($help)
{
	$tpl->help	= $this->action('help', ['info' => $info]);
	return;
}

$tag	= $this->unwrap_link($page);

if ($linking_to = $_GET['linking_to'] ?? '')
{
	$tpl->to_target = $this->link('/' . $linking_to);

	if ([$pages, $pagination] = $this->load_pages_linking($linking_to, $tag))
	{
		foreach ($pages as $page)
		{
			if (!$this->db->hide_locked || $this->has_access('read', $page['page_id']))
			{
				$tpl->to_l_link = $this->link('/' . $page['tag'], '', '/' . $page['tag']);
			}
		}
	}
	else
	{
		$tpl->to_none = true;
	}
}
else
{
	if ([$pages, $pagination] = $load_wanted($tag, $max))
	{
		if (is_array($pages))
		{
			$tpl->enter('w_');
			$tpl->pagination_text	= $pagination['text'];
			$tpl->offset			= $pagination['offset'] + 1;

			foreach ($pages as $page)
			{
				$page_parent	= mb_substr($page['wanted_tag'], 0, mb_strrpos($page['wanted_tag'], '/'));
				$page_id_parent	= $this->get_page_id($page_parent);

				if (!$this->db->hide_locked || $this->has_access('read', $page_id_parent))
				{
					// update the referrer count for the WantedPage, we need to take pages the user is not allowed to view out of the total
					$count = 0;

					if ([$ref_pages, $pagination] = $this->load_pages_linking($page['wanted_tag'], $tag))
					{
						foreach ($ref_pages as $ref_page)
						{
							if (!$this->db->hide_locked || $this->has_access('read', $ref_page['page_id']))
							{
								$count++;
							}
						}
					}

					// If no pages are referring to the WantedPage it means the referrers are all locked so don't show the link at all
					if ($count > 0)
					{
						$tpl->l_link	= $this->link('/' . $page['wanted_tag']);
						$tpl->l_href	= $this->href('', '', ['linking_to' => $page['wanted_tag']]);
						$tpl->l_count	= $count;
					}
				}
			}

			$tpl->leave();
		}
	}
	else
	{
		$tpl->none = true;
	}
}
