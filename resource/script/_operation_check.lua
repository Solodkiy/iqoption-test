-- Скрипт проверяет что операция с определённым id не выполнялась ранее

local OPERATIONS_LIST = 'operations_list'
local OPERATIONS_SET = 'operations_set'
local MAX_OPERATIONS_SET_SIZE = 1000

if operationId ~= '' then
    if redis.call('SISMEMBER', OPERATIONS_SET, operationId) == 1 then
        return 'DOUBLE_OPERATION'
    end

    redis.call('SADD', OPERATIONS_SET, operationId)
    redis.call('RPUSH', OPERATIONS_LIST, operationId)
    while redis.call('LLEN', OPERATIONS_LIST) > MAX_OPERATIONS_SET_SIZE do
        local elementToDel = redis.call('LPOP', OPERATIONS_LIST)
        redis.call('SREM', OPERATIONS_SET, elementToDel)
    end
end
