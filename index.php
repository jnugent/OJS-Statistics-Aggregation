<?php

/**
 * @defgroup plugins_generic_statisticsAggregation
 */

/**
 * @file plugins/generic/statisticsAggregation/index.php
 *
 * Copyright (c) 2003-2010 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_statisticsAggregation
 * @brief Wrapper for Statistics Aggregation plugin.
 *
 */

// $Id$


require_once('StatisticsAggregationPlugin.inc.php');

return new StatisticsAggregationPlugin();

?>