local fromAccountId = tonumber(ARGV[1])
local toAccountId = tonumber(ARGV[2])
local amount = tonumber(ARGV[3])
local operationId = ARGV[4]
local BALANCE_KEY = 'account_balance'

{{operation_check}}

if amount < 0 then
  return 'INCORRECT_AMMOUNT'
end

local currentFromBalance = 0
if redis.call('HEXISTS', BALANCE_KEY, fromAccountId) == 1 then
    currentFromBalance = tonumber(redis.call('hget', BALANCE_KEY, fromAccountId))
end

local newFromBalance = currentFromBalance - amount
if newFromBalance >= 0 then
  redis.call('hset', BALANCE_KEY, fromAccountId, newFromBalance)
  redis.call('hincrby', BALANCE_KEY, toAccountId, amount)
  return 'OK'
else
  return 'NO_MONEY'
end

