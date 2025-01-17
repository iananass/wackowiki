<?php

if (!defined('IN_WACKO'))
{
	exit;
}

// action/mygroups.php

// set defaults
$nomark		??= 0;

$my_groups = function ($user_id, $groups) use ($tpl)
{
	foreach ($groups as $group_name => $members)
	{
		if (in_array ($user_id, $members))
		{
			$tpl->n_group = $this->group_link($group_name, true, false);
		}
	}
};

if ($user = $this->get_user())
{
	if (!$nomark)
	{
		$tpl->mark			= true;
		$tpl->mark_username	= $user['user_name'];
		$tpl->emark			= true;
	}

	$my_groups($user['user_id'], $this->db->groups);
}
