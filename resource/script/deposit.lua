local accountId = tonumber(ARGV[1])
local amount = tonumber(ARGV[2])
local operationId = ARGV[3]
local BALANCE_KEY = 'account_balance'

{{operation_check}}

if amount < 0 then
  return 'INCORRECT_AMMOUNT'
end

redis.call('hincrby', BALANCE_KEY, accountId, amount)
return 'OK'
