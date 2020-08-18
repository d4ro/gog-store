<?php

namespace GogStore\Pattern;

interface Observable
{
    function attach(Observer $observer): void;

    function detach(Observer $observer): void;

    function notifyChanged(): void;
}