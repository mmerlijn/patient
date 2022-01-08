<?php

namespace mmerlijn\patient\Observers;

use Illuminate\Support\Facades\DB;
use mmerlijn\patient\Models\Requester;

class RequesterObserver
{
    use NameObserverTrait;

    /**
     * Handle the patient "created" event.
     *
     * @param \mmerlijn\patient\Models\Requester $requester
     * @return void
     */
    public function creating(Requester $requester)
    {
        $r = $requester->relations ?? [];
        $r[] = $requester->agbcode;
        $r = array_unique($r);
        sort($r);
        //DB::enableQueryLog();
        //dd(DB::getQueryLog());
        foreach (Requester::whereIn('agbcode', $r)->where('agbcode', '<>', $requester->agbcode)->get() as $relation) {
            $relation->relations = $r;
            $relation->saveQuietly(); //without oberservers called
        }
        $requester->relations = $r;

        $tmp = $this->nameSplitter($requester->lastname, $requester->prefix);
        $requester->lastname = $tmp['lastname'];
        $requester->prefix = $tmp['prefix'];
    }

    /**
     * Handle the patient "created" event.
     *
     * @param \mmerlijn\patient\Models\Requester $requester
     * @return void
     */
    public function updating(Requester $requester)
    {
        if ($requester->isDirty('relations')) { //
            $r = $requester->relations ?? [];

            $r[] = $requester->agbcode;
            $r = array_unique($r);
            sort($r);
            //sync new relations
            foreach (Requester::whereIn('agbcode', $r)->where('agbcode', "<>", $requester->agbcode)->get() as $related) {
                $related->relations = $r;
                $related->saveQuietly();
            }
            $requester->relations = $r;
            $diff = array_diff($requester->getOriginal('relations'), $r);
            //sync removed relations
            foreach (Requester::whereIn('agbcode', $diff)->get() as $related) {
                $r = $related->relations;
                if (($key = array_search($requester->agbcode, $r)) !== false) {
                    unset($r[$key]);
                }
                $related->relations = $r;
                $related->saveQuietly(); //without oberservers called
            }
        }
        if ($requester->isDirty('lastname') or $requester->isDirty('prefix')) {
            $tmp = $this->nameSplitter($requester->lastname, $requester->prefix);
            $requester->lastname = $tmp['lastname'];
            $requester->prefix = $tmp['prefix'];
        }
    }

    /**
     * Handle the patient "created" event.
     *
     * @param \mmerlijn\patient\Models\Requester $requester
     * @return void
     */
    public function deleting(Requester $requester)
    {
        $r = [];

        $r[] = $requester->agbcode;
        $requester->relations = $r;
        $diff = array_diff($requester->getOriginal('relations'), $r);
        //sync removed relations
        foreach (Requester::whereIn('agbcode', $diff)->get() as $related) {
            $r = $related->relations;
            if (($key = array_search($requester->agbcode, $r)) !== false) {
                unset($r[$key]);
            }
            $related->relations = $r;
            $related->saveQuietly(); //without oberservers called
        }

    }
}