<?php

namespace Appto\TelegramBot\Support\Resolvers;

use Appto\TelegramBot\Data\ChatMemberAdministrator;
use Appto\TelegramBot\Data\ChatMemberBanned;
use Appto\TelegramBot\Data\ChatMemberLeft;
use Appto\TelegramBot\Data\ChatMemberMember;
use Appto\TelegramBot\Data\ChatMemberOwner;
use Appto\TelegramBot\Data\ChatMemberRestricted;

class ChatMemberResolve extends GenericResolver
{
    public function __construct()
    {
        parent::__construct([
            'creator' => ChatMemberOwner::class,
            'administrator' => ChatMemberAdministrator::class,
            'member' => ChatMemberMember::class,
            'restricted' => ChatMemberRestricted::class,
            'left' => ChatMemberLeft::class,
            'kicked' => ChatMemberBanned::class,
        ], 'status');
    }
}
