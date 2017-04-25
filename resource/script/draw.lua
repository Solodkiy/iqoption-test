local accountId = tonumber(ARGV[1])
local amount = tonumber(ARGV[2])
local operationId = ARGV[3]
local BALANCE_KEY = 'account_balance'

{{operation_check}}

if amount < 0 then
  return 'INCORRECT_AMMOUNT'
end

local currentBalance = 0
if redis.call('HEXISTS', BALANCE_KEY, accountId) == 1 then
    currentBalance = tonumber(redis.call('hget', BALANCE_KEY, accountId))
end

local newBalance = currentBalance - amount
if newBalance >= 0 then
  redis.call('hset', BALANCE_KEY, accountId, newBalance)
  return 'OK'
else
  return 'NO_MONEY'
end