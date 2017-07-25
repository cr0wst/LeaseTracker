<?php
/**
 * Created by IntelliJ IDEA.
 * User: steve
 * Date: 7/20/2017
 * Time: 2:07 PM
 */

namespace LeaseTracker\DataTransfer;

// Not sure how the PHP community feels about this.  I believe under the hood passing an array or a DTO is effectively
// the same thing.  This is commonly referred to as a POJO or a Bean in the Java community.

// I, personally, like them because they can establish what stateful objects you're passing around your stateless
// services without waiting for run-time.

/**
 * Data Transfer Object for Cost data used by the CostService.
 * @package LeaseTracker\DataTransfer
 */
class CostData
{
    // Alright, so I did some googling on this one and it seems like PSR-2 didn't mention anything about getters/setters
    // In fact! It looks like this is the internet's opinionated way to do it.  I will make the argument that
    // a getter/setter probably isn't needed if it's just going to do assignment or return the value as-is.
    // if there's mutation pre-assignment, however, I'd be more inclined to either use the magic getter/setter or write them.
    public $milesRemaining;
    public $milesPerMonth;
    public $cost;
    public $predictedOverage;
    public $monthsRemaining;
    public $endDate;

    // It's kind of funny because as I flesh out this DTO (which I'm treating as a Java Bean) I'm finding myself
    // almost wanting to go with return array(...) instead...  DTO would probably be more beneficial if it had inherited
    // information.  For my simple application it's probably not as necessary.

    /**
     * @return string Number formatted cost.
     */
    public function getFormattedCost():string
    {
        // Hardcoded dollar-signs should probably not be recommended.  But apparently money_format doesn't work on windows.
        return '$ '.number_format($this->cost, 2, '.', ',');
    }
}