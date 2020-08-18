<?php

namespace GogStore\Pattern;

interface Observer
{
    function notify(Observable $observable): void;
}